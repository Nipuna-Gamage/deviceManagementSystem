<?php

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $user = User::create([
        'name' => 'Test Admin',
        'email' => 'admin@example.test',
        'password' => 'StrongPassword123!',
        'role' => 'admin',
        'is_active' => true,
    ]);
    $this->actingAs($user);
});

it('shows the device inventory page', function () {
    $this->get(route('devices.index'))->assertOk()->assertSee('Device inventory');
});

it('registers a device and generates an asset tag', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Dell Latitude 7440',
        'category' => 'Laptop',
        'manufacturer' => 'Dell',
        'model' => 'Latitude 7440',
        'serial_number' => 'DELL-TEST-001',
        'status' => 'available',
        'condition' => 'good',
    ]);

    $device = Device::first();
    expect($device)->not->toBeNull();
    expect($device->asset_tag)->toBe('AST-' . str_pad((string) $device->id, 5, '0', STR_PAD_LEFT));
    $response->assertRedirect(route('devices.show', $device));
});

it('requires a unique serial number', function () {
    Device::create([
        'name' => 'Existing device',
        'category' => 'Laptop',
        'serial_number' => 'SERIAL-001',
        'asset_tag' => 'AST-00001',
        'status' => 'available',
        'condition' => 'good',
    ]);

    $this->from(route('register'))->post(route('register.store'), [
        'name' => 'Duplicate device',
        'category' => 'Laptop',
        'serial_number' => 'SERIAL-001',
        'status' => 'available',
        'condition' => 'good',
    ])->assertSessionHasErrors('serial_number');
});

it('updates and deletes a device', function () {
    $device = Device::create([
        'name' => 'Old name',
        'category' => 'Laptop',
        'serial_number' => 'SERIAL-UPDATE-001',
        'asset_tag' => 'AST-00002',
        'status' => 'available',
        'condition' => 'good',
    ]);

    $this->put(route('devices.update', $device), [
        'name' => 'Updated laptop',
        'category' => 'Laptop',
        'serial_number' => 'SERIAL-UPDATE-001',
        'status' => 'assigned',
        'condition' => 'excellent',
    ])->assertRedirect(route('devices.show', $device));

    expect($device->fresh()->name)->toBe('Updated laptop');

    $this->delete(route('devices.destroy', $device))->assertRedirect(route('devices.index'));
    $this->assertDatabaseMissing('devices', ['id' => $device->id]);
});

it('creates departments and employees', function () {
    $department = \App\Models\Department::create(['name' => 'Information Technology', 'code' => 'IT']);

    $this->post(route('employees.store'), [
        'employee_code' => 'EMP-001',
        'first_name' => 'Nimal',
        'last_name' => 'Perera',
        'email' => 'nimal@example.test',
        'department_id' => $department->id,
    ])->assertRedirect();

    $this->assertDatabaseHas('employees', [
        'employee_code' => 'EMP-001',
        'department_id' => $department->id,
    ]);
});

it('assigns an available device and records its return', function () {
    $department = \App\Models\Department::create(['name' => 'Operations', 'code' => 'OPS']);
    $employee = \App\Models\Employee::create([
        'employee_code' => 'EMP-002', 'first_name' => 'Saman', 'last_name' => 'Silva',
        'email' => 'saman@example.test', 'department_id' => $department->id,
    ]);
    $device = Device::create([
        'name' => 'HP ProBook', 'category' => 'Laptop', 'serial_number' => 'HP-PHASE3-001',
        'asset_tag' => 'AST-90001', 'status' => 'available', 'condition' => 'good',
    ]);

    $this->post(route('assignments.store'), [
        'device_id' => $device->id,
        'employee_id' => $employee->id,
        'assigned_at' => now()->toDateString(),
    ])->assertRedirect();

    expect($device->fresh()->status)->toBe('assigned');
    $assignment = \App\Models\DeviceAssignment::firstOrFail();
    expect($assignment->returned_at)->toBeNull();

    $this->patch(route('assignments.return', $assignment), [
        'returned_at' => now()->toDateString(),
        'return_notes' => 'Returned in good condition',
    ])->assertRedirect();

    expect($device->fresh()->status)->toBe('available');
    expect($assignment->fresh()->returned_at)->not->toBeNull();
});

it('prevents assigning a device that is already assigned', function () {
    $employee = \App\Models\Employee::create([
        'employee_code' => 'EMP-003', 'first_name' => 'Kamal', 'last_name' => 'Fernando',
        'email' => 'kamal@example.test',
    ]);
    $device = Device::create([
        'name' => 'Assigned desktop', 'category' => 'Desktop', 'serial_number' => 'DESK-PHASE3-001',
        'asset_tag' => 'AST-90002', 'status' => 'assigned', 'condition' => 'good',
    ]);

    $this->from(route('assignments.index'))->post(route('assignments.store'), [
        'device_id' => $device->id,
        'employee_id' => $employee->id,
        'assigned_at' => now()->toDateString(),
    ])->assertSessionHasErrors('device_id');
});

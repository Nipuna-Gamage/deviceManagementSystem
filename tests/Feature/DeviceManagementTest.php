<?php

use App\Models\Device;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

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

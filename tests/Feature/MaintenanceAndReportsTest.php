<?php

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows a manager to create a maintenance record and marks an available device for maintenance', function () {
    $user = User::factory()->create(['role' => 'manager', 'is_active' => true]);
    $device = Device::create([
        'name' => 'Test laptop', 'category' => 'Laptop', 'serial_number' => 'TEST-MAINT-001',
        'asset_tag' => 'AST-TEST-001', 'status' => 'available', 'condition' => 'good',
    ]);

    $this->actingAs($user)->post(route('maintenance.store'), [
        'device_id' => $device->id, 'title' => 'Battery replacement',
        'type' => 'repair', 'priority' => 'high',
    ])->assertRedirect();

    expect($device->fresh()->status)->toBe('maintenance');
    $this->assertDatabaseHas('maintenance_records', ['device_id' => $device->id, 'title' => 'Battery replacement']);
});

it('protects report exports behind authentication', function () {
    $this->get(route('reports.devices'))->assertRedirect(route('login'));
});

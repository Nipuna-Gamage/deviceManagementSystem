<?php

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function dashboardUser(): User
{
    return User::create([
        'name' => 'Dashboard User',
        'email' => 'dashboard-'.uniqid().'@example.test',
        'password' => 'StrongPassword123!',
        'role' => 'manager',
        'is_active' => true,
    ]);
}

it('renders a useful empty dashboard state', function () {
    $this->actingAs(dashboardUser())
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Your inventory is ready for its first device')
        ->assertSee(route('devices.index'))
        ->assertSee('No recent activity yet')
        ->assertDontSee('Need a hand?')
        ->assertDontSee('Explore workspace');
});

it('renders live dashboard data and working status links', function () {
    $device = Device::create([
        'name' => 'Dashboard laptop',
        'category' => 'Laptop',
        'serial_number' => 'DASHBOARD-'.uniqid(),
        'asset_tag' => 'AST-DASH-'.uniqid(),
        'status' => 'available',
        'condition' => 'good',
    ]);

    $this->actingAs(dashboardUser())
        ->get(route('home'))
        ->assertOk()
        ->assertSee('You currently have 1 device in inventory, with 1 available for assignment.')
        ->assertSee('Device registered')
        ->assertSee($device->name)
        ->assertSee(route('devices.index', ['status' => 'assigned']))
        ->assertSee(route('devices.index', ['status' => 'available']));
});

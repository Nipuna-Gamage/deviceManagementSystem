<?php

use App\Models\Device;
use App\Models\MaintenanceRecord;
use App\Models\User;
use App\Notifications\AssetAlertNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

function phaseSixUser(string $role = 'admin'): User
{
    return User::create([
        'name' => 'Phase Six '.$role,
        'email' => $role.'-'.uniqid().'@example.test',
        'password' => 'StrongPassword123!',
        'role' => $role,
        'is_active' => true,
    ]);
}

it('requires authentication for global search and exposes security headers', function () {
    $this->get(route('search', ['q' => 'Dell']))->assertRedirect(route('login'));

    $this->actingAs(phaseSixUser('staff'))
        ->get(route('search', ['q' => 'Dell']))
        ->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN');
});

it('renders the analytics report for an authenticated user', function () {
    $this->actingAs(phaseSixUser('manager'))
        ->get(route('reports.index'))
        ->assertOk()
        ->assertSee('Inventory distribution')
        ->assertSee('New devices by month');
});

it('sends warranty notifications to active administrators and managers', function () {
    Notification::fake();
    $admin = phaseSixUser('admin');
    $device = Device::create([
        'name' => 'Warranty test laptop', 'category' => 'Laptop',
        'serial_number' => 'PHASE6-WARRANTY-'.uniqid(), 'asset_tag' => 'AST-P6-'.uniqid(),
        'status' => 'available', 'condition' => 'good',
        'warranty_expiry' => now()->addDays(7)->toDateString(),
    ]);

    $this->artisan('assetflow:send-alerts')->assertSuccessful();

    Notification::assertSentTo($admin, AssetAlertNotification::class);
});

it('does not allow staff to create maintenance requests', function () {
    $staff = phaseSixUser('staff');
    $device = Device::create([
        'name' => 'Restricted laptop', 'category' => 'Laptop',
        'serial_number' => 'PHASE6-STAFF-'.uniqid(), 'asset_tag' => 'AST-P6S-'.uniqid(),
        'status' => 'available', 'condition' => 'good',
    ]);

    $this->actingAs($staff)->post(route('maintenance.store'), [
        'device_id' => $device->id, 'title' => 'Unauthorized repair',
        'type' => 'repair', 'priority' => 'medium',
    ])->assertForbidden();
    $this->assertDatabaseMissing('maintenance_records', ['title' => 'Unauthorized repair']);
});

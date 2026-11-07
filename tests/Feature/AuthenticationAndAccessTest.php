<?php

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createAccessUser(string $role = 'staff', bool $active = true): User
{
    return User::create([
        'name' => ucfirst($role).' User',
        'email' => $role.'-'.uniqid().'@example.test',
        'password' => 'StrongPassword123!',
        'role' => $role,
        'is_active' => $active,
    ]);
}

it('allows an active user to sign in and redirects to the dashboard', function () {
    $user = createAccessUser('staff');
    $this->post(route('login.store'), ['email' => $user->email, 'password' => 'StrongPassword123!'])
        ->assertRedirect(route('home'));
    $this->assertAuthenticatedAs($user);
});

it('rejects inactive users at sign in', function () {
    $user = createAccessUser('staff', false);
    $this->from(route('login'))->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'StrongPassword123!',
    ])->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('prevents staff from opening administrator pages', function () {
    $this->actingAs(createAccessUser('staff'))
        ->get(route('users.index'))
        ->assertForbidden();
});

it('allows administrators to open user management', function () {
    $this->actingAs(createAccessUser('admin'))
        ->get(route('users.index'))
        ->assertOk();
});

it('records successful write requests in the audit log', function () {
    $admin = createAccessUser('admin');
    $this->actingAs($admin)->post(route('departments.store'), [
        'name' => 'Information Technology',
        'code' => 'IT-'.random_int(100, 999),
    ])->assertRedirect();

    expect(AuditLog::where('user_id', $admin->id)->where('action', 'create')->exists())->toBeTrue();
});

it('signs the user out and invalidates the session', function () {
    $this->actingAs(createAccessUser('admin'))
        ->post(route('logout'))
        ->assertRedirect(route('login'));
    $this->assertGuest();
});

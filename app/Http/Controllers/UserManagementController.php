<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        return view('users.index', ['users' => User::latest()->paginate(15)]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:10', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'manager', 'staff'])],
        ]);
        User::create($data);
        return back()->with('success', 'User account created.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is($request->user()), 422, 'You cannot change your own account here.');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'manager', 'staff'])],
            'is_active' => ['required', 'boolean'],
            'password' => ['nullable', 'string', 'min:10', 'confirmed'],
        ]);
        if ($user->role === 'admin' && ($data['role'] !== 'admin' || ! $data['is_active'])
            && User::where('role', 'admin')->where('is_active', true)->count() <= 1) {
            return back()->withErrors(['user' => 'At least one active administrator must remain in the system.']);
        }
        if (filled($data['password'] ?? null)) {
            $user->password = Hash::make($data['password']);
        }
        unset($data['password']);
        $user->update($data);
        return back()->with('success', 'User account updated.');
    }
}

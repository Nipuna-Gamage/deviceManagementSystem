<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        $credentials['is_active'] = true;

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'The provided credentials are incorrect or the account is inactive.']);
        }

        $request->session()->regenerate();
        $request->user()->forceFill(['last_login_at' => now()])->save();
        AuditLog::create([
            'user_id' => $request->user()->id, 'action' => 'login', 'method' => 'POST',
            'route' => 'login', 'description' => 'User signed in successfully.',
            'ip_address' => $request->ip(), 'user_agent' => substr((string) $request->userAgent(), 0, 1000),
            'response_status' => 302,
        ]);

        return redirect()->intended(route('home'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        if ($request->user()) {
            AuditLog::create([
                'user_id' => $request->user()->id, 'action' => 'logout', 'method' => 'POST',
                'route' => 'logout', 'description' => 'User signed out.',
                'ip_address' => $request->ip(), 'user_agent' => substr((string) $request->userAgent(), 0, 1000),
                'response_status' => 302,
            ]);
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('status', 'You have been signed out.');
    }
}

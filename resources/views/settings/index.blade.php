@extends('layouts.app')
@section('title', 'Settings')
@section('breadcrumb', 'Settings')
@section('content')
<div class="page-heading settings-heading">
    <div><div class="eyebrow">WORKSPACE CONFIGURATION</div><h1>Settings center</h1><p>Manage your profile, workspace preferences, and system defaults.</p></div>
</div>
@if(session('success'))<div class="settings-alert settings-success" role="status">✓ {{ session('success') }}</div>@endif
@if($errors->any())<div class="settings-alert settings-error" role="alert"><strong>Could not save your changes.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="settings-layout">
    <aside class="settings-nav" aria-label="Settings sections">
        <div class="settings-nav-title">SETTINGS</div>
        @foreach([
            'profile' => ['◉', 'Profile'],
            'organization' => ['▣', 'Organization'],
            'notifications' => ['♧', 'Notifications'],
            'appearance' => ['◒', 'Appearance'],
            'security' => ['⊙', 'Security'],
            'system' => ['⚙', 'System'],
        ] as $key => $item)
            @if(in_array($key, ['organization', 'system'], true) && auth()->user()->role !== 'admin') @continue @endif
            <a href="{{ route('settings.index', ['section' => $key]) }}" class="{{ $section === $key ? 'active' : '' }}"><span>{{ $item[0] }}</span>{{ $item[1] }}</a>
        @endforeach
        <div class="settings-nav-note"><strong>{{ ucfirst(auth()->user()->role) }} account</strong><small>Changes are protected by your account permissions.</small></div>
    </aside>
    <div class="settings-content">
        @if($section === 'profile')
        <section class="settings-card">
            <div class="settings-card-head"><div><h2>Profile settings</h2><p>Update the personal information used across AssetFlow.</p></div><span class="settings-card-icon">◉</span></div>
            <form method="POST" action="{{ route('settings.profile') }}" enctype="multipart/form-data" class="settings-form">
                @csrf @method('PUT')
                <div class="profile-upload"><div class="settings-avatar">@if($user->profile_photo)<img src="{{ asset('storage/'.$user->profile_photo) }}" alt="Current profile photo">@else{{ strtoupper(substr($user->name, 0, 2)) }}@endif</div><div><label class="button button-secondary file-button">Upload photo<input type="file" name="profile_photo" accept=".jpg,.jpeg,.png,.webp"></label><small>JPG, PNG or WebP. Maximum 2 MB.</small></div></div>
                <div class="settings-grid"><label>Full name<input name="name" value="{{ old('name', $user->name) }}" required></label><label>Email address<input type="email" name="email" value="{{ old('email', $user->email) }}" required></label></div>
                <div class="settings-readonly"><span>Account status</span><strong class="status-active">● Active</strong><span>Role</span><strong>{{ ucfirst($user->role) }}</strong></div>
                <div class="settings-actions"><button class="button button-primary" type="submit">Save profile changes</button><a class="button button-secondary" href="{{ route('settings.index', ['section' => 'profile']) }}">Cancel</a></div>
            </form>
        </section>
        @elseif($section === 'organization' && auth()->user()->role === 'admin')
        <section class="settings-card">
            <div class="settings-card-head"><div><h2>Organization settings</h2><p>Control the identity and contact details for your workspace.</p></div><span class="settings-card-icon">▣</span></div>
            <form method="POST" action="{{ route('settings.organization') }}" enctype="multipart/form-data" class="settings-form">
                @csrf @method('PUT')
                <div class="profile-upload"><div class="settings-logo">@if($organization->logo_path)<img src="{{ asset('storage/'.$organization->logo_path) }}" alt="Organization logo">@else▣@endif</div><div><label class="button button-secondary file-button">Upload logo<input type="file" name="organization_logo" accept=".jpg,.jpeg,.png,.webp"></label><small>JPG, PNG or WebP. Maximum 2 MB.</small></div></div>
                <div class="settings-grid"><label>Organization name<input name="organization_name" value="{{ old('organization_name', $organization->organization_name) }}" required></label><label>Contact email<input type="email" name="contact_email" value="{{ old('contact_email', $organization->contact_email) }}"></label><label>Contact phone<input name="contact_phone" value="{{ old('contact_phone', $organization->contact_phone) }}"></label><label class="full-width">Address<textarea name="address" rows="3">{{ old('address', $organization->address) }}</textarea></label></div>
                <div class="settings-actions"><button class="button button-primary" type="submit">Save organization details</button><a class="button button-secondary" href="{{ route('settings.index', ['section' => 'organization']) }}">Cancel</a></div>
            </form>
        </section>
        @elseif(in_array($section, ['notifications', 'appearance'], true))
        <section class="settings-card">
            <div class="settings-card-head"><div><h2>{{ ucfirst($section) }} preferences</h2><p>Choose how AssetFlow looks and keeps you informed.</p></div><span class="settings-card-icon">{{ $section === 'notifications' ? '♧' : '◒' }}</span></div>
            <form method="POST" action="{{ route('settings.preferences') }}" class="settings-form">
                @csrf @method('PUT')
                @if($section === 'notifications')
                <div class="settings-option"><div><strong>Email notifications</strong><small>Allow AssetFlow to send operational updates.</small></div><label class="switch"><input type="hidden" name="email_notifications" value="0"><input type="checkbox" name="email_notifications" value="1" @checked($preferences->email_notifications)><span></span></label></div>
                <div class="settings-option"><div><strong>Warranty expiry alerts</strong><small>Receive reminders before warranties expire.</small></div><label class="switch"><input type="hidden" name="warranty_alerts" value="0"><input type="checkbox" name="warranty_alerts" value="1" @checked($preferences->warranty_alerts)><span></span></label></div>
                <div class="settings-option"><div><strong>Maintenance due reminders</strong><small>Be notified about open maintenance work.</small></div><label class="switch"><input type="hidden" name="maintenance_reminders" value="0"><input type="checkbox" name="maintenance_reminders" value="1" @checked($preferences->maintenance_reminders)><span></span></label></div>
                <div class="settings-option"><div><strong>Assignment notifications</strong><small>Get updates when devices are assigned.</small></div><label class="switch"><input type="hidden" name="assignment_notifications" value="0"><input type="checkbox" name="assignment_notifications" value="1" @checked($preferences->assignment_notifications)><span></span></label></div>
                <div class="settings-option"><div><strong>Return notifications</strong><small>Get updates when assigned devices return.</small></div><label class="switch"><input type="hidden" name="return_notifications" value="0"><input type="checkbox" name="return_notifications" value="1" @checked($preferences->return_notifications)><span></span></label></div>
                @else
                <div class="theme-choices"><label class="theme-choice"><input type="radio" name="theme" value="light" @checked($preferences->theme === 'light')><span>☼<strong>Light</strong><small>Bright workspace</small></span></label><label class="theme-choice dark-choice"><input type="radio" name="theme" value="dark" @checked($preferences->theme === 'dark')><span>☾<strong>Dark</strong><small>Low-light workspace</small></span></label></div>
                <label>Accent color<input class="color-input" type="color" name="accent_color" value="{{ $preferences->accent_color }}"></label>
                <div class="settings-option"><div><strong>Compact sidebar</strong><small>Use a smaller navigation footprint.</small></div><label class="switch"><input type="hidden" name="sidebar_collapsed" value="0"><input type="checkbox" name="sidebar_collapsed" value="1" @checked($preferences->sidebar_collapsed)><span></span></label></div>
                @endif
                <div class="settings-actions"><button class="button button-primary" type="submit">Save preferences</button><a class="button button-secondary" href="{{ route('settings.index', ['section' => $section]) }}">Cancel</a></div>
            </form>
        </section>
        @elseif($section === 'security')
        <section class="settings-card">
            <div class="settings-card-head"><div><h2>Security settings</h2><p>Keep your account credentials protected.</p></div><span class="settings-card-icon">⊙</span></div>
            <form method="POST" action="{{ route('settings.password') }}" class="settings-form">
                @csrf @method('PUT')
                <div class="settings-grid"><label>Current password<input type="password" name="current_password" required autocomplete="current-password"></label><span></span><label>New password<input type="password" name="password" required minlength="8" autocomplete="new-password"></label><label>Confirm new password<input type="password" name="password_confirmation" required autocomplete="new-password"></label></div>
                <p class="form-hint">Use at least 8 characters. Your current password is required to make this change.</p>
                <div class="settings-readonly"><span>Account status</span><strong class="status-active">● {{ $user->is_active ? 'Active' : 'Inactive' }}</strong><span>Last sign in</span><strong>{{ $user->last_login_at?->format('Y-m-d H:i') ?? 'Not available' }}</strong></div>
                <div class="settings-actions"><button class="button button-primary" type="submit">Change password</button><a class="button button-secondary" href="{{ route('settings.index', ['section' => 'security']) }}">Cancel</a></div>
            </form>
            <div class="security-note"><strong>Two-factor authentication</strong><p>Not enabled in this installation. A secure 2FA provider can be added when required; no simulated controls are shown.</p></div>
        </section>
        @elseif($section === 'system' && auth()->user()->role === 'admin')
        <section class="settings-card">
            <div class="settings-card-head"><div><h2>System settings</h2><p>Set defaults used by the AssetFlow workspace.</p></div><span class="settings-card-icon">⚙</span></div>
            <form method="POST" action="{{ route('settings.system') }}" class="settings-form">
                @csrf @method('PUT')
                <div class="settings-grid"><label>Date format<select name="date_format"><option value="Y-m-d" @selected($organization->date_format === 'Y-m-d')>2026-11-08 (Y-m-d)</option><option value="d/m/Y" @selected($organization->date_format === 'd/m/Y')>08/11/2026 (d/m/Y)</option><option value="m/d/Y" @selected($organization->date_format === 'm/d/Y')>11/08/2026 (m/d/Y)</option></select></label><label>Timezone<select name="timezone">@foreach($timezones as $timezone)<option value="{{ $timezone }}" @selected(($organization->timezone ?: config('app.timezone')) === $timezone)>{{ $timezone }}</option>@endforeach</select></label><label>Currency<input name="currency" value="{{ old('currency', $organization->currency ?: 'USD') }}" maxlength="3" required></label><label>Asset tag prefix<input name="asset_tag_prefix" value="{{ old('asset_tag_prefix', $organization->asset_tag_prefix ?: 'AST-') }}" required></label></div>
                <div class="system-info"><div><span>Application</span><strong>AssetFlow</strong></div><div><span>Framework</span><strong>Laravel {{ app()->version() }}</strong></div><div><span>Environment</span><strong>{{ app()->environment() }}</strong></div></div>
                <div class="settings-actions"><button class="button button-primary" type="submit">Save system settings</button><a class="button button-secondary" href="{{ route('settings.index', ['section' => 'system']) }}">Cancel</a></div>
            </form>
        </section>
        @endif
    </div>
</div>
@endsection

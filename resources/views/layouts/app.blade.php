<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Overview') · AssetFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="{{ route('home') }}">
            <span class="brand-mark"><svg viewBox="0 0 32 32" aria-hidden="true"><path d="M7 7.5 16 3l9 4.5v17L16 29l-9-4.5v-17Z"/><path d="m11 12 5-2.5 5 2.5v8l-5 2.5-5-2.5v-8Z"/></svg></span>
            <span class="brand-name">asset<span>flow</span><small>ASSET MANAGEMENT</small></span>
        </a>
        <div class="workspace-label">WORKSPACE</div>
        <nav class="side-nav" aria-label="Main navigation">
            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"><span class="nav-icon">⌂</span>Overview</a>
            <a href="{{ route('devices.index') }}" class="nav-link {{ request()->routeIs('devices.*') ? 'active' : '' }}"><span class="nav-icon">▣</span>Device inventory</a>
            <a href="{{ route('register') }}" class="nav-link {{ request()->routeIs('register*') ? 'active' : '' }}"><span class="nav-icon">＋</span>Register device</a>
            <a href="{{ route('assignments.index') }}" class="nav-link {{ request()->routeIs('assignments.*') ? 'active' : '' }}"><span class="nav-icon">⇄</span>Assignments</a>
            <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}"><span class="nav-icon">♙</span>Employees</a>
            <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}"><span class="nav-icon">▤</span>Departments</a>
            <div class="nav-divider"></div>
            <div class="workspace-label">MANAGE</div>
            <a href="{{ route('maintenance.index') }}" class="nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}"><span class="nav-icon">⚒</span>Maintenance</a>
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"><span class="nav-icon">▥</span>Reports</a>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"><span class="nav-icon">♙</span>User management</a>
            <a href="{{ route('audit.index') }}" class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}"><span class="nav-icon">◷</span>Audit logs</a>
            @endif
            <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"><span class="nav-icon">⚙</span>Settings</a>
        </nav>
        <div class="sidebar-bottom">
            <div class="help-card"><div class="help-icon">✦</div><strong>Need a hand?</strong><p>Find your way around AssetFlow.</p><a href="{{ route('reports.index') }}">Explore workspace <span>↗</span></a></div>
            <div class="profile-row"><div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div><div class="profile-copy"><strong>{{ auth()->user()->name }}</strong><small>{{ ucfirst(auth()->user()->role) }}</small></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-link" type="submit" title="Sign out" aria-label="Sign out">↗</button></form></div>
        </div>
    </aside>

    <div class="main-column">
        <header class="topbar">
            <button class="mobile-menu" type="button" aria-label="Toggle navigation" data-menu-toggle>☰</button>
            <div class="breadcrumbs"><span>Workspace</span><b>/</b><strong>@yield('breadcrumb', 'Overview')</strong></div>
            <div class="topbar-actions">
            <form class="global-search" action="{{ route('search') }}" method="GET" role="search"><input type="search" name="q" placeholder="Search assets, people..." aria-label="Search AssetFlow" value="{{ request('q') }}"><button type="submit" aria-label="Search">⌕</button></form>
            <span class="system-status"><i></i> All systems normal</span><a class="icon-button notification-link" href="{{ route('notifications.index') }}" aria-label="Notifications">♧@if(auth()->user()->unreadNotifications()->exists())<span class="notification-dot"></span>@endif</a><div class="top-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div></div>
        </header>
        <main class="page-content">@yield('content')</main>
        <footer class="footer"><span>© {{ date('Y') }} AssetFlow</span><span>Device management, made clearer.</span></footer>
    </div>
</div>
<div class="sidebar-scrim" data-menu-close></div>
</body>
</html>

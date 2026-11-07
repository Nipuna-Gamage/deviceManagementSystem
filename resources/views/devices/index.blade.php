@extends('layouts.app')
@section('title', 'Device inventory')
@section('breadcrumb', 'Device inventory')
@section('content')
<div class="page-heading"><div><div class="eyebrow">ASSET REGISTER</div><h1>Device inventory</h1><p>Search, review and manage your organization’s devices.</p></div><a class="button button-primary" href="{{ route('register') }}">＋ Register a device</a></div>
@if(session('success'))<div class="form-notice" role="status"><span>✓</span><p>{{ session('success') }}</p></div>@endif
<div class="panel recent-panel">
    <form method="GET" action="{{ route('devices.index') }}" class="inventory-filters">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search name, asset tag, serial number…" aria-label="Search devices">
        <select name="category" aria-label="Filter by category"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>@endforeach</select>
        <select name="status" aria-label="Filter by status"><option value="">All statuses</option>@foreach(['available'=>'Available','assigned'=>'Assigned','maintenance'=>'Maintenance','retired'=>'Retired'] as $value=>$label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select>
        <button class="button button-primary" type="submit">Filter</button><a class="button button-secondary" href="{{ route('devices.index') }}">Clear</a>
    </form>
    <div class="table-scroll"><table class="data-table"><thead><tr><th>DEVICE</th><th>ASSET TAG</th><th>CATEGORY</th><th>SERIAL NUMBER</th><th>STATUS</th><th>ACTION</th></tr></thead><tbody>
    @forelse($devices as $device)
        <tr><td><span class="device-cell"><span class="device-thumb laptop">▣</span><span><strong>{{ $device->name }}</strong><small>{{ $device->manufacturer ?: 'Manufacturer not set' }} {{ $device->model ? '· '.$device->model : '' }}</small></span></span></td><td class="muted-cell">{{ $device->asset_tag }}</td><td>{{ $device->category }}</td><td>{{ $device->serial_number }}</td><td><span class="status-pill {{ $device->status }}"><i></i>{{ ucfirst($device->status) }}</span></td><td><a class="text-link" href="{{ route('devices.show', $device) }}">View →</a></td></tr>
    @empty
        <tr><td colspan="6" class="empty-state">No devices found. Register your first device to get started.</td></tr>
    @endforelse
    </tbody></table></div>
    <div class="pagination-wrap">{{ $devices->links() }}</div>
</div>
@endsection

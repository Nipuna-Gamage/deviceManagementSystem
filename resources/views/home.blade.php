@extends('layouts.app')
@section('title', 'Overview')
@section('breadcrumb', 'Overview')
@section('content')
<div class="page-heading">
    <div><div class="eyebrow">MONDAY, YOUR WORKSPACE AT A GLANCE</div><h1>Good day, Admin <span class="wave">✦</span></h1><p>Here’s what’s happening with your organization’s devices.</p></div>
    <a class="button button-primary" href="{{ route('register') }}"><span>＋</span> Register a device</a>
</div>
<div class="notice-banner"><div class="notice-symbol">✧</div><div><strong>{{ $totalDevices === 0 ? 'Your inventory is ready for its first device' : "You currently have {$totalDevices} " . \Illuminate\Support\Str::plural('device', $totalDevices) . " in inventory, with {$availableDevices} available for assignment." }}</strong><p>{{ $totalDevices === 0 ? 'Register a device to start tracking your organization’s assets.' : 'Live summary based on the devices currently stored in your database.' }}</p></div><a href="{{ route('devices.index') }}">Explore inventory <span>→</span></a></div>
<section class="stats-grid" aria-label="Inventory summary">
    <a class="stat-card stat-card-link" href="{{ route('devices.index') }}"><div class="stat-top"><span>Total devices</span><span class="stat-icon blue">▣</span></div><div class="stat-value">{{ $totalDevices }}</div><div class="stat-foot"><span class="trend neutral">Live</span><span>database total</span></div><div class="mini-bars"><i style="height:35%"></i><i style="height:48%"></i><i style="height:42%"></i><i style="height:65%"></i><i style="height:54%"></i><i style="height:78%"></i><i style="height:68%"></i><i style="height:92%"></i></div></a>
    <a class="stat-card stat-card-link" href="{{ route('devices.index', ['status' => 'assigned']) }}"><div class="stat-top"><span>Assigned devices</span><span class="stat-icon violet">⇄</span></div><div class="stat-value">{{ $assignedDevices }}</div><div class="stat-foot"><span class="trend neutral">Live</span><span>currently assigned</span></div><div class="progress-track"><span style="width:{{ $totalDevices > 0 ? min(100, round(($assignedDevices / $totalDevices) * 100)) : 0 }}%"></span></div><small class="stat-caption">Currently assigned</small></a>
    <a class="stat-card stat-card-link" href="{{ route('devices.index', ['status' => 'available']) }}"><div class="stat-top"><span>Available devices</span><span class="stat-icon green">✓</span></div><div class="stat-value">{{ $availableDevices }}</div><div class="stat-foot"><span class="trend neutral">Live</span><span>ready to assign</span></div><div class="progress-track green-track"><span style="width:{{ $totalDevices > 0 ? min(100, round(($availableDevices / $totalDevices) * 100)) : 0 }}%"></span></div><small class="stat-caption">Ready to be assigned</small></a>
    <a class="stat-card stat-card-link" href="{{ route('maintenance.index') }}"><div class="stat-top"><span>Needs attention</span><span class="stat-icon orange">!</span></div><div class="stat-value">{{ $maintenanceDevices }}</div><div class="stat-foot"><span class="trend warning">Needs attention</span><span>maintenance devices</span></div><div class="attention-line"><span></span><span></span><span></span><span></span><span></span></div><small class="stat-caption">Items requiring follow-up</small></a>
</section>
<section class="dashboard-grid" style="margin-bottom:20px">
    <article class="panel" style="padding:20px"><div class="panel-heading"><div><h2>Device status analytics</h2><p>Current inventory distribution</p></div></div><div style="height:230px"><canvas id="homeStatusChart" aria-label="Device status analytics"></canvas></div></article>
    <article class="panel" style="padding:20px"><div class="panel-heading"><div><h2>Registration trend</h2><p>New devices added over the last six months</p></div></div><div style="height:230px"><canvas id="homeMonthlyChart" aria-label="Monthly device registration trend"></canvas></div></article>
</section>
<section class="dashboard-grid">
    <article class="panel inventory-panel"><div class="panel-heading"><div><h2>Inventory overview</h2><p>Device distribution by category</p></div><a class="text-link" href="{{ route('devices.index') }}">View inventory <span>→</span></a></div>
        <div class="inventory-content"><div class="donut-wrap"><div class="donut"><div><strong>{{ $totalDevices }}</strong><small>Total assets</small></div></div></div><div class="legend-list">@foreach($categoryStats as $item)<div><i class="legend-dot dot-blue"></i><span>{{ $item->category }}</span><strong>{{ $item->total }}</strong></div>@endforeach @if($totalDevices === 0)<small class="stat-caption">Register devices to see category totals.</small>@endif</div></div>
    </article>
    <article class="panel activity-panel"><div class="panel-heading"><div><h2>Quick actions</h2><p>Common tasks, right at your fingertips</p></div></div><div class="quick-actions"><a href="{{ route('register') }}"><span class="quick-icon q-blue">＋</span><span><strong>Register a device</strong><small>Add a new asset to inventory</small></span><b>→</b></a><a href="{{ route('employees.index') }}"><span class="quick-icon q-violet">♙</span><span><strong>Add an employee</strong><small>Create a person for assignments</small></span><b>→</b></a><a href="{{ route('departments.index') }}"><span class="quick-icon q-green">▤</span><span><strong>Add a department</strong><small>Organize your people directory</small></span><b>→</b></a><a href="{{ route('assignments.index') }}"><span class="quick-icon q-violet">⇄</span><span><strong>Assign a device</strong><small>Allocate an asset to an employee</small></span><b>→</b></a><a href="{{ route('maintenance.index') }}"><span class="quick-icon q-orange">⚒</span><span><strong>Create maintenance request</strong><small>Record a repair or service</small></span><b>→</b></a><a href="{{ route('reports.index') }}"><span class="quick-icon q-green">▥</span><span><strong>View reports</strong><small>Review your asset activity</small></span><b>→</b></a></div></article>
</section>
<section class="panel recent-panel"><div class="panel-heading"><div><h2>Recently added devices</h2><p>A snapshot of the latest assets in your workspace</p></div><a class="text-link" href="{{ route('devices.index') }}">See all devices <span>→</span></a></div><div class="table-scroll"><table class="data-table"><thead><tr><th>DEVICE</th><th>ASSET TAG</th><th>CATEGORY</th><th>ASSIGNED TO</th><th>STATUS</th></tr></thead><tbody>
@forelse($recentDevices as $device)
<tr><td><span class="device-cell"><span class="device-thumb laptop">▰</span><span><strong>{{ $device->name }}</strong><small>{{ $device->manufacturer ?: 'Manufacturer not set' }}</small></span></span></td><td class="muted-cell">{{ $device->asset_tag }}</td><td>{{ $device->category }}</td><td>{{ $device->activeAssignment?->employee?->full_name ?? '—' }}</td><td><span class="status-pill {{ $device->status }}"><i></i>{{ ucfirst($device->status) }}</span></td></tr>
@empty
<tr><td colspan="5" class="empty-state">No devices registered yet. Start by adding your first device.</td></tr>
@endforelse
</tbody></table></div></section>
<section class="panel recent-panel dashboard-activity"><div class="panel-heading"><div><h2>Recent activity</h2><p>Latest changes across your AssetFlow workspace</p></div>@if(auth()->user()->role === 'admin')<a class="text-link" href="{{ route('audit.index') }}">View audit logs <span>→</span></a>@else<a class="text-link" href="{{ route('reports.index') }}">View reports <span>→</span></a>@endif</div><div class="activity-feed">
@forelse($recentActivity as $activity)
<a class="activity-item" href="{{ $activity['url'] }}"><span class="activity-icon {{ $activity['tone'] }}">{{ $activity['icon'] }}</span><span class="activity-copy"><strong>{{ $activity['type'] }} · {{ $activity['title'] }}</strong><small>{{ $activity['detail'] }}</small></span><time datetime="{{ $activity['occurred_at']?->toIso8601String() }}">{{ $activity['occurred_at']?->diffForHumans() }}</time></a>
@empty
<div class="activity-empty">No recent activity yet. Register a device or create an assignment to get started.</div>
@endforelse
</div></section>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.Chart) return;
    new Chart(document.getElementById('homeStatusChart'), {
        type: 'doughnut',
        data: { labels: ['Available','Assigned','Maintenance','Retired'], datasets: [{ data: @json($dashboardStatusChartData), borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' }, tooltip: { callbacks: { label: function (context) { return ' ' + context.label + ': ' + context.formattedValue + ' devices'; } } } }, cutout: '65%' }
    });
    new Chart(document.getElementById('homeMonthlyChart'), {
        type: 'line',
        data: { labels: @json($dashboardMonthlyLabels), datasets: [{ label: 'New devices', data: @json($dashboardMonthlyChartData), borderColor: '#536df0', backgroundColor: 'rgba(83,109,240,.12)', pointBackgroundColor: '#536df0', tension: .35, fill: true }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: function (context) { return ' New devices: ' + context.formattedValue; } } } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });
});
</script>
@endsection

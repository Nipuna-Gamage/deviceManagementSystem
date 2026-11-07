@extends('layouts.app')
@section('title', 'Reports')
@section('breadcrumb', 'Reports')
@section('content')
<div class="page-heading"><div><div class="eyebrow">INSIGHTS & EXPORTS</div><h1>Reports & analytics</h1><p>A live snapshot of your organization's asset inventory and activity.</p></div></div>
<div class="stats-grid">
 <div class="stat-card"><div class="stat-top">Total devices</div><div class="stat-value">{{ array_sum($deviceStats->toArray()) }}</div></div>
 <div class="stat-card"><div class="stat-top">Active assignments</div><div class="stat-value">{{ $activeAssignments }}</div></div>
 <div class="stat-card"><div class="stat-top">Active employees</div><div class="stat-value">{{ $activeEmployees }}</div></div>
 <div class="stat-card"><div class="stat-top">Completed maintenance cost</div><div class="stat-value" style="font-size:21px">{{ number_format((float)$maintenanceCost, 2) }}</div></div>
</div>
<div class="panel" style="padding:22px;margin-bottom:20px">
 <h2 style="font-size:15px;margin:0 0 16px">Export data</h2>
 <div style="display:flex;gap:12px;flex-wrap:wrap">
  <a class="button button-primary" href="{{ route('reports.devices') }}">↓ Export device inventory (CSV)</a>
  <a class="button button-primary" href="{{ route('reports.assignments') }}">↓ Export assignment history (CSV)</a>
 </div>
 <p style="font-size:10px;color:#8994a8;margin:12px 0 0">CSV files can be opened in Microsoft Excel, Google Sheets or LibreOffice Calc.</p>
</div>
<div class="grid-2" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;margin-bottom:20px">
 <div class="panel" style="padding:22px;min-width:0"><h2 style="font-size:15px;margin:0 0 15px">Inventory distribution</h2><div style="height:260px"><canvas id="statusChart" aria-label="Device inventory by status"></canvas></div></div>
 <div class="panel" style="padding:22px;min-width:0"><h2 style="font-size:15px;margin:0 0 15px">New devices by month</h2><div style="height:260px"><canvas id="monthlyChart" aria-label="Devices registered in the last six months"></canvas></div></div>
</div>
<div class="panel" style="padding:22px;margin-bottom:20px">
 <h2 style="font-size:15px;margin:0 0 15px">Inventory by status</h2>
 @php($total = max(1, array_sum($deviceStats->toArray())))
 @foreach(['available'=>'Available','assigned'=>'Assigned','maintenance'=>'Maintenance','retired'=>'Retired'] as $key=>$label)
  @php($count = (int)($deviceStats[$key] ?? 0))
  <div style="display:grid;grid-template-columns:110px 1fr 45px;gap:12px;align-items:center;margin:13px 0;font-size:11px;color:#667287">
   <span>{{ $label }}</span><div style="height:9px;border-radius:20px;background:#eef1f7;overflow:hidden"><div style="height:100%;width:{{ round($count/$total*100,1) }}%;background:#536df0;border-radius:20px"></div></div><strong>{{ $count }}</strong>
  </div>
 @endforeach
</div>
<div class="panel" style="padding:22px">
 <h2 style="font-size:15px;margin:0 0 15px">Devices by category</h2>
 <div style="overflow-x:auto"><table class="data-table"><thead><tr><th>Category</th><th>Device count</th></tr></thead><tbody>
 @forelse($categoryStats as $row)<tr><td>{{ $row->category }}</td><td>{{ $row->total }}</td></tr>@empty<tr><td colspan="2">No device data available.</td></tr>@endforelse
 </tbody></table></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusLabels = ['Available', 'Assigned', 'Maintenance', 'Retired'];
    const statusValues = @json($statusChartData);
    const monthlyLabels = @json($monthlyChartLabels);
    const monthlyValues = @json($monthlyChartData);
    if (window.Chart) {
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: { labels: statusLabels, datasets: [{ data: statusValues, borderWidth: 0, hoverOffset: 7 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, cutout: '68%' }
        });
        new Chart(document.getElementById('monthlyChart'), {
            type: 'bar',
            data: { labels: monthlyLabels, datasets: [{ label: 'New devices', data: monthlyValues, borderRadius: 6 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    }
});
</script>
<style>@media(max-width:800px){.grid-2{grid-template-columns:1fr!important}}</style>
@endsection

@extends('layouts.app')
@section('title','Audit logs')
@section('breadcrumb','Audit logs')
@section('content')
<div class="page-heading"><div><p class="eyebrow">SECURITY & COMPLIANCE</p><h1>Audit logs</h1><p>Review successful changes made by authenticated users.</p></div></div>
<section class="panel"><div class="panel-heading"><div><h2>Recent activity</h2><p>{{ $logs->total() }} recorded event(s)</p></div></div><div class="table-wrap"><table class="data-table"><thead><tr><th>When</th><th>User</th><th>Action</th><th>Request</th><th>IP address</th><th>Result</th></tr></thead><tbody>@forelse($logs as $log)<tr><td>{{ $log->created_at->format('d M Y H:i:s') }}</td><td>{{ $log->user?->name ?? 'Deleted user' }}</td><td><span class="status-pill">{{ ucfirst($log->action) }}</span></td><td><strong>{{ $log->method }}</strong> {{ $log->route }}</td><td>{{ $log->ip_address ?? '—' }}</td><td>{{ $log->response_status ?? '—' }}</td></tr>@empty<tr><td colspan="6">No activity recorded yet.</td></tr>@endforelse</tbody></table></div>{{ $logs->links() }}</section>
@endsection

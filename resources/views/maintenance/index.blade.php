@extends('layouts.app')
@section('title', 'Maintenance')
@section('breadcrumb', 'Maintenance')
@section('content')
<div class="page-heading"><div><div class="eyebrow">ASSET CARE</div><h1>Maintenance</h1><p>Track repairs, inspections and preventive service for your devices.</p></div></div>
@if(session('success'))<div class="notice-banner"><div class="notice-symbol">✓</div><div><strong>{{ session('success') }}</strong></div></div>@endif
@if($errors->any())<div class="notice-banner"><div><strong>Please check the form:</strong><p>{{ $errors->first() }}</p></div></div>@endif
<div class="stats-grid">
 <div class="stat-card"><div class="stat-top">Open / In progress</div><div class="stat-value">{{ $openCount }}</div></div>
 <div class="stat-card"><div class="stat-top">Completed jobs</div><div class="stat-value">{{ $completedCount }}</div></div>
 <div class="stat-card"><div class="stat-top">Tracked devices</div><div class="stat-value">{{ $devices->count() }}</div></div>
 <div class="stat-card"><div class="stat-top">Maintenance records</div><div class="stat-value">{{ $records->total() }}</div></div>
</div>
<div class="panel" style="padding:22px;margin-bottom:20px">
 <h2 style="margin:0 0 16px;font-size:15px">Create maintenance request</h2>
 <form method="POST" action="{{ route('maintenance.store') }}" class="form-grid">@csrf
  <label>Device<select name="device_id" required><option value="">Select device</option>@foreach($devices as $device)<option value="{{ $device->id }}" @selected(old('device_id')==$device->id)>{{ $device->asset_tag }} · {{ $device->name }} ({{ ucfirst($device->status) }})</option>@endforeach</select></label>
  <label>Issue / task<input name="title" value="{{ old('title') }}" maxlength="160" required placeholder="e.g. Replace laptop battery"></label>
  <label>Type<select name="type" required><option value="repair">Repair</option><option value="preventive">Preventive</option><option value="inspection">Inspection</option></select></label>
  <label>Priority<select name="priority" required><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option><option value="critical">Critical</option></select></label>
  <label>Scheduled date<input type="date" name="scheduled_date" value="{{ old('scheduled_date') }}"></label>
  <label>Estimated cost<input type="number" name="cost" min="0" step="0.01" value="{{ old('cost') }}" placeholder="0.00"></label>
  <label class="full-width">Description<textarea name="description" rows="2" placeholder="Describe the issue or planned service">{{ old('description') }}</textarea></label>
  <div class="full-width"><button class="button button-primary" type="submit">＋ Create request</button></div>
 </form>
</div>
<div class="panel" style="padding:22px">
 <div style="display:flex;justify-content:space-between;gap:15px;align-items:center;margin-bottom:16px"><h2 style="margin:0;font-size:15px">Maintenance history</h2>
 <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap"><input name="search" value="{{ request('search') }}" placeholder="Search device or task"><select name="status"><option value="">All statuses</option>@foreach(['open','in_progress','completed','cancelled'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>@endforeach</select><button class="button button-primary">Filter</button></form></div>
 <div style="overflow-x:auto"><table class="data-table"><thead><tr><th>Device</th><th>Task</th><th>Priority</th><th>Scheduled</th><th>Status / Update</th></tr></thead><tbody>
 @forelse($records as $record)<tr><td><strong>{{ $record->device?->asset_tag }}</strong><br><small>{{ $record->device?->name }}</small></td><td>{{ $record->title }}<br><small>{{ ucfirst($record->type) }}</small></td><td>{{ ucfirst($record->priority) }}</td><td>{{ $record->scheduled_date?->format('d M Y') ?? '—' }}</td><td>
 <form method="POST" action="{{ route('maintenance.update',$record) }}" style="display:flex;gap:6px;flex-wrap:wrap">@csrf @method('PATCH')
 <select name="status">@foreach(['open','in_progress','completed','cancelled'] as $status)<option value="{{ $status }}" @selected($record->status===$status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>@endforeach</select>
 <select name="priority">@foreach(['low','medium','high','critical'] as $priority)<option value="{{ $priority }}" @selected($record->priority===$priority)>{{ ucfirst($priority) }}</option>@endforeach</select>
 <input type="date" name="completed_date" value="{{ $record->completed_date?->format('Y-m-d') }}" aria-label="Completed date">
 <input type="number" name="cost" min="0" step="0.01" value="{{ $record->cost }}" placeholder="Cost" aria-label="Actual cost">
 <input name="resolution" value="{{ $record->resolution }}" placeholder="Resolution" aria-label="Resolution">
 <button class="button button-primary" type="submit">Save</button></form>
 </td></tr>@empty<tr><td colspan="5">No maintenance records found.</td></tr>@endforelse
 </tbody></table></div><div style="margin-top:18px">{{ $records->links() }}</div>
</div>
@endsection

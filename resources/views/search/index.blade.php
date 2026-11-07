@extends('layouts.app')
@section('title', 'Search')
@section('breadcrumb', 'Search')
@section('content')
<div class="page-heading"><div><div class="eyebrow">WORKSPACE SEARCH</div><h1>Search AssetFlow</h1><p>Find devices, employees, departments and maintenance records.</p></div></div>
<form action="{{ route('search') }}" method="GET" class="panel" style="padding:18px;display:flex;gap:10px;margin-bottom:20px">
 <input type="search" name="q" value="{{ $term }}" autofocus placeholder="Enter at least 2 characters..." style="flex:1;min-width:0;border:1px solid #e7eaf0;border-radius:7px;padding:12px">
 <button class="button button-primary" type="submit">Search</button>
</form>
@if(mb_strlen($term)<2)<div class="panel" style="padding:25px;color:#8791a4">Type at least two characters to search your workspace.</div>
@else
 @php($total = $devices->count()+$employees->count()+$departments->count()+$maintenance->count())
 <p style="font-size:12px;color:#8791a4;margin:0 0 15px">{{ $total }} result(s) for <strong>{{ $term }}</strong></p>
 <div class="panel" style="padding:20px;margin-bottom:15px"><h2 style="font-size:14px">Devices ({{ $devices->count() }})</h2>
 @forelse($devices as $device)<p><a href="{{ route('devices.show',$device) }}">{{ $device->asset_tag }} · {{ $device->name }}</a> <small>— {{ $device->serial_number }}</small></p>@empty<p class="stat-caption">No matching devices.</p>@endforelse</div>
 <div class="panel" style="padding:20px;margin-bottom:15px"><h2 style="font-size:14px">Employees ({{ $employees->count() }})</h2>
 @forelse($employees as $employee)<p>{{ $employee->full_name }} <small>— {{ $employee->employee_code }} · {{ $employee->department?->name ?? 'No department' }}</small></p>@empty<p class="stat-caption">No matching employees.</p>@endforelse</div>
 <div class="panel" style="padding:20px;margin-bottom:15px"><h2 style="font-size:14px">Departments ({{ $departments->count() }})</h2>
 @forelse($departments as $department)<p>{{ $department->name }} <small>— {{ $department->code }}</small></p>@empty<p class="stat-caption">No matching departments.</p>@endforelse</div>
 <div class="panel" style="padding:20px"><h2 style="font-size:14px">Maintenance ({{ $maintenance->count() }})</h2>
 @forelse($maintenance as $record)<p>{{ $record->title }} <small>— {{ $record->device?->asset_tag }} · {{ ucfirst($record->status) }}</small></p>@empty<p class="stat-caption">No matching maintenance records.</p>@endforelse</div>
@endif
@endsection

@extends('layouts.app')
@section('title', $device->name)
@section('breadcrumb', 'Device details')
@section('content')
<div class="page-heading"><div><div class="eyebrow">DEVICE DETAILS · {{ $device->asset_tag }}</div><h1>{{ $device->name }}</h1><p>{{ $device->manufacturer }} {{ $device->model }}</p></div><div class="heading-actions"><a class="button button-secondary" href="{{ route('devices.index') }}">Back to inventory</a><a class="button button-primary" href="{{ route('devices.edit', $device) }}">Edit device</a></div></div>
@if(session('success'))<div class="form-notice"><span>✓</span><p>{{ session('success') }}</p></div>@endif
<div class="panel detail-panel"><h2>Device information</h2><dl class="detail-grid">
@foreach(['Asset tag'=>$device->asset_tag,'Category'=>$device->category,'Serial number'=>$device->serial_number,'Manufacturer'=>$device->manufacturer,'Model'=>$device->model,'Status'=>ucfirst($device->status),'Condition'=>ucfirst($device->condition),'Purchase date'=>$device->purchase_date?->format('d M Y'),'Warranty expiry'=>$device->warranty_expiry?->format('d M Y'),'Notes'=>$device->notes] as $label=>$value)<div><dt>{{ $label }}</dt><dd>{{ $value ?: '—' }}</dd></div>@endforeach
</dl>
<form method="POST" action="{{ route('devices.destroy', $device) }}" onsubmit="return confirm('Are you sure you want to delete this device?')" class="delete-form">@csrf @method('DELETE')<button class="button button-danger" type="submit">Delete device</button></form></div>
@endsection

@extends('layouts.app')
@section('title', 'Edit device')
@section('breadcrumb', 'Edit device')
@section('content')
<div class="page-heading"><div><div class="eyebrow">ASSET REGISTER</div><h1>Edit device</h1><p>Update the information for {{ $device->asset_tag }}.</p></div><a class="button button-secondary" href="{{ route('devices.show', $device) }}">Cancel</a></div>
@include('devices.form', ['device' => $device, 'action' => route('devices.update', $device), 'method' => 'PUT', 'buttonText' => 'Save changes'])
@endsection

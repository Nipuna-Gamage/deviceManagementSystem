@extends('layouts.app')
@section('title', 'Register device')
@section('breadcrumb', 'Register device')
@section('content')
<div class="page-heading"><div><div class="eyebrow">DEVICE INVENTORY</div><h1>Register a device</h1><p>Add a new asset to your organization’s inventory.</p></div><a class="button button-secondary" href="{{ route('devices.index') }}">← Back to inventory</a></div>
@include('devices.form', ['device' => null, 'action' => route('register.store'), 'method' => 'POST', 'buttonText' => 'Register device'])
@endsection

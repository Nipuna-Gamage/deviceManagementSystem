@extends('layouts.app')
@section('title', $pageTitle)
@section('breadcrumb', $pageTitle)
@section('content')
<div class="page-heading"><div><div class="eyebrow">ASSETFLOW WORKSPACE</div><h1>{{ $pageTitle }}</h1><p>{{ $pageDescription }}</p></div></div>
<div class="panel coming-soon"><div class="coming-icon">✦</div><h2>This workspace is coming together</h2><p>This section is part of the AssetFlow redesign. Its full workflow will be implemented in the upcoming development phases.</p><a class="button button-primary" href="{{ route('home') }}">← Back to overview</a></div>
@endsection

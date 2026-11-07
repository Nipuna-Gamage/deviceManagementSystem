@extends('layouts.app')
@section('title', 'Notifications')
@section('breadcrumb', 'Notifications')
@section('content')
<div class="page-heading"><div><div class="eyebrow">ALERT CENTER</div><h1>Notifications</h1><p>Warranty reminders and maintenance alerts for your workspace.</p></div>
<form method="POST" action="{{ route('notifications.read-all') }}">@csrf<button class="button button-primary" type="submit">Mark all as read</button></form></div>
@if(session('success'))<div class="notice-banner"><strong>{{ session('success') }}</strong></div>@endif
<div class="panel" style="padding:20px">
 @forelse($notifications as $notification)
 <div style="padding:15px 5px;border-bottom:1px solid #edf0f5;display:flex;gap:15px;align-items:flex-start">
  <span class="stat-icon {{ ($notification->data['kind'] ?? '')==='warranty' ? 'orange' : 'blue' }}"> {{ ($notification->data['kind'] ?? '')==='warranty' ? '!' : '⚒' }} </span>
  <div style="flex:1"><strong style="font-size:12px">{{ $notification->data['subject'] ?? 'AssetFlow notification' }}</strong><p style="font-size:11px;color:#788397;margin:5px 0">{{ $notification->data['message'] ?? '' }}</p><small style="color:#a0a8b6">{{ $notification->created_at->format('d M Y, H:i') }} · {{ $notification->read_at ? 'Read' : 'Unread' }}</small></div>
  @if(!$notification->read_at)<form method="POST" action="{{ route('notifications.read',$notification->id) }}">@csrf<button class="button button-primary" type="submit">View</button></form>@else<a class="text-link" href="{{ $notification->data['url'] ?? route('notifications.index') }}">Open</a>@endif
 </div>
 @empty<p style="color:#8791a4;font-size:12px">No notifications yet.</p>@endforelse
 <div style="margin-top:18px">{{ $notifications->links() }}</div>
</div>
@endsection

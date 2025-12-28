@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Notifications</h1>
            @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-primary">Mark All as Read</button>
            </form>
            @endif
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                @forelse($notifications as $notification)
                    <div class="alert {{ $notification->read_at ? 'alert-secondary' : 'alert-info' }} d-flex justify-content-between align-items-center">
                        <div>
                            <div class="d-flex align-items-center">
                                <iconify-icon icon="{{ $notification->data['icon'] ?? 'solar:bell-bold' }}" class="me-2 fs-4"></iconify-icon>
                                <div>
                                    <strong class="d-block">{{ $notification->data['message'] ?? 'New Notification' }}</strong>
                                    <small>{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            @if(isset($notification->data['url']) && $notification->data['url'] !== '#')
                                <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-light">View</a>
                            @endif
                            @if(!$notification->read_at)
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Mark as Read">
                                        <iconify-icon icon="solar:check-read-linear"></iconify-icon>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <iconify-icon icon="solar:bell-off-linear" class="fs-1 text-muted mb-3"></iconify-icon>
                        <p class="text-muted">No notifications found.</p>
                    </div>
                @endforelse

                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

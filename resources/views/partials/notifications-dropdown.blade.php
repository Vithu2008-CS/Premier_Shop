@if($notifications->isEmpty())
    <div class="px-3 py-4 text-center text-muted">
        <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
        <small>No new notifications</small>
    </div>
@else
    <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
        @foreach($notifications as $notification)
            <a href="{{ route('notifications.read', $notification) }}" class="list-group-item list-group-item-action {{ $notification->isRead() ? 'bg-light text-muted' : '' }}">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <h6 class="mb-1 fw-bold" style="font-size: 0.9rem;">
                        <i class="bi {{ $notification->icon }} me-1 {{ !$notification->isRead() ? 'text-primary' : '' }}"></i>
                        {{ $notification->title }}
                    </h6>
                    <small class="text-muted" style="font-size: 0.75rem;">{{ $notification->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-0 small {{ $notification->isRead() ? 'text-muted' : '' }}">{{ Str::limit($notification->message, 80) }}</p>
            </a>
        @endforeach
    </div>
    
    <div class="p-2 border-top text-center bg-light">
        <form action="{{ route('notifications.readAll') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm btn-link text-decoration-none">Mark all as read</button>
        </form>
    </div>
@endif

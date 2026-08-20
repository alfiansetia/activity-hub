@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    @include('partials.breadcrumb', [
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'bi bi-house-door'],
            ['label' => 'Notifications'],
        ],
    ])

    {{-- Notifications Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-bell me-2"></i>Notifications</h6>
            <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Send Notification
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Title</th>
                            <th class="d-none d-md-table-cell">Message</th>
                            <th class="d-none d-md-table-cell">Sent By</th>
                            <th>Recipients</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notification)
                            <tr>
                                <td>
                                    <span class="badge bg-{{ $notification->type_color }}">
                                        <i class="{{ $notification->type_icon }} me-1"></i>
                                        {{ ucfirst($notification->type) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $notification->title }}</div>
                                    <div class="d-md-none text-muted small text-truncate" style="max-width: 200px;">
                                        {{ Str::limit($notification->message, 60) }}
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="text-muted small">{{ Str::limit($notification->message, 80) }}</span>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <span class="small">{{ $notification->createdBy->name ?? 'System' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $notification->users()->count() }} users</span>
                                </td>
                                <td>
                                    <span
                                        class="small text-muted">{{ $notification->created_at->format('d M Y H:i') }}</span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <form method="POST"
                                            action="{{ route('admin.notifications.destroy', $notification) }}"
                                            class="delete-notification-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
                                    No notifications sent yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($notifications->hasPages())
            <div class="card-footer bg-white">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.delete-notification-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formEl = this;

                Swal.fire({
                    title: 'Delete Notification?',
                    text: 'This notification will be permanently deleted for all users.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        formEl.submit();
                    }
                });
            });
        });
    </script>
@endpush

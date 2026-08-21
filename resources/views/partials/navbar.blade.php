<header class="top-navbar">
    <div class="d-flex align-items-center">
        {{-- Toggle sidebar (desktop + mobile) --}}
        <button class="navbar-toggle" onclick="toggleSidebar()" title="Toggle sidebar">
            <i class="bi bi-list"></i>
        </button>

        {{-- Page Title --}}
        <h1 class="navbar-title">@yield('title', 'Dashboard')</h1>
    </div>

    <div class="navbar-actions">
        {{-- Notifications --}}
        <div class="dropdown" id="notificationDropdown">
            <button class="navbar-icon-btn" data-bs-toggle="dropdown" aria-expanded="false" id="notificationBtn"
                title="Notifications" onclick="loadNotifications()">
                <i class="bi bi-bell"></i>
                <span class="navbar-badge" id="notificationBadge">0</span>
            </button>
            <div class="dropdown-menu dropdown-menu-end notification-dropdown notification-dropdown-menu">
                <div class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2">
                    <span class="fw-semibold">Notifications</span>
                    <button class="btn btn-link btn-sm text-decoration-none p-0" onclick="markAllAsRead(event)"
                        id="markAllReadBtn" style="display: none;">
                        Mark all as read
                    </button>
                </div>
                <div class="dropdown-divider my-0"></div>
                <div id="notificationList" class="notification-list" style="max-height: 380px; overflow-y: auto;">
                    <div class="text-center text-muted py-4">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="dropdown-divider my-0"></div>
                <div class="text-center py-2">
                    <span class="text-muted small" id="notificationEmpty" style="display: none;">No notifications
                        yet</span>
                </div>
            </div>
        </div>

        {{-- User dropdown --}}
        <div class="dropdown">
            <button class="navbar-user-btn" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="navbar-user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="navbar-user-name">{{ auth()->user()->name }}</span>
                <i class="bi bi-chevron-down" style="font-size: 0.65rem; color: var(--text-muted);"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end mt-1">
                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="bi bi-person me-2"></i> Profile
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

{{-- Notification Detail Modal --}}
<div class="modal fade" id="notificationDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <span id="modalTypeBadge" class="badge"></span>
                    <h6 class="modal-title mb-0" id="modalTitle"></h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modalMessage" class="mb-3"></p>
                <div class="d-flex justify-content-between align-items-center text-muted small">
                    <span><i class="bi bi-person me-1"></i><span id="modalSender"></span></span>
                    <span><i class="bi bi-clock me-1"></i><span id="modalTime"></span></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let notifCurrentPage = 1;
        let notifHasMore = false;
        let notifLoadingMore = false;

        function loadNotifications() {
            notifCurrentPage = 1;

            // Show loading spinner
            document.getElementById('notificationList').innerHTML = `
                <div class="text-center text-muted py-4">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>`;

            fetch('{{ route('api.notifications.fetch') }}?page=1', {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    notifHasMore = data.has_more;
                    notifCurrentPage = data.current_page;
                    updateBadge(data.unread_count);
                    renderNotifications(data.notifications, data.unread_count > 0);
                })
                .catch(() => {
                    document.getElementById('notificationList').innerHTML =
                        '<div class="text-center text-danger py-3"><i class="bi bi-exclamation-circle me-1"></i>Failed to load</div>';
                });
        }

        function loadMoreNotifications() {
            if (!notifHasMore || notifLoadingMore) return;

            notifLoadingMore = true;
            const loadMoreBtn = document.getElementById('loadMoreBtn');
            if (loadMoreBtn) {
                loadMoreBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Loading...';
                loadMoreBtn.disabled = true;
            }

            fetch(`{{ route('api.notifications.fetch') }}?page=${notifCurrentPage + 1}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    notifHasMore = data.has_more;
                    notifCurrentPage = data.current_page;
                    appendNotifications(data.notifications);
                })
                .catch(() => {})
                .finally(() => {
                    notifLoadingMore = false;
                });
        }

        function updateBadge(count) {
            const badge = document.getElementById('notificationBadge');
            badge.textContent = count;
            badge.style.display = count > 0 ? '' : 'none';
        }

        function renderNotifications(notifications, hasUnread) {
            const list = document.getElementById('notificationList');
            const markAllBtn = document.getElementById('markAllReadBtn');
            const emptyMsg = document.getElementById('notificationEmpty');

            markAllBtn.style.display = hasUnread ? '' : 'none';

            if (notifications.length === 0) {
                list.innerHTML = '';
                emptyMsg.style.display = '';
                return;
            }

            emptyMsg.style.display = 'none';

            list.innerHTML = buildNotificationHtml(notifications) + buildLoadMoreHtml();
        }

        function appendNotifications(notifications) {
            const list = document.getElementById('notificationList');
            // Remove the existing "Load More" button before appending
            const existingLoadMore = document.getElementById('loadMoreBtn');
            if (existingLoadMore) {
                existingLoadMore.closest('.notification-load-more').remove();
            }
            // Append new items + new "Load More" if needed
            list.insertAdjacentHTML('beforeend', buildNotificationHtml(notifications) + buildLoadMoreHtml());
        }

        function buildNotificationHtml(notifications) {
            return notifications.map(n => `
            <div class="notification-item d-flex align-items-start gap-2 px-3 py-2 ${n.is_read ? '' : 'notification-unread'}"
                style="cursor: pointer;" onclick="showNotificationDetail(${n.id})">
                <div class="flex-shrink-0 mt-1">
                    <span class="badge bg-${n.type_color} rounded-circle p-1">
                        <i class="${n.type_icon}" style="font-size: 0.6rem;"></i>
                    </span>
                </div>
                <div class="flex-grow-1 min-width-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="fw-semibold small text-truncate me-2">${escapeHtml(n.title)}</span>
                        ${!n.is_read ? '<span class="badge bg-primary rounded-pill flex-shrink-0" style="width: 8px; height: 8px; padding: 0;"></span>' : ''}
                    </div>
                    <div class="text-muted small text-truncate">${escapeHtml(n.message.substring(0, 80))}${n.message.length > 80 ? '...' : ''}</div>
                    <div class="text-muted" style="font-size: 0.7rem;">${n.time_ago}</div>
                </div>
            </div>
        `).join('');
        }

        function buildLoadMoreHtml() {
            if (!notifHasMore) return '';
            return `
            <div class="notification-load-more text-center py-2 border-top">
                <button class="btn btn-link btn-sm text-decoration-none" id="loadMoreBtn" onclick="loadMoreNotifications(event)">
                    Load more
                </button>
            </div>`;
        }

        function showNotificationDetail(id) {
            // Close dropdown
            const dropdown = bootstrap.Dropdown.getInstance(document.getElementById('notificationBtn'));
            if (dropdown) dropdown.hide();

            fetch(`/api/notifications/${id}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    // Set modal content
                    document.getElementById('modalTitle').textContent = data.title;
                    document.getElementById('modalMessage').textContent = data.message;
                    document.getElementById('modalSender').textContent = data.sender;
                    document.getElementById('modalTime').textContent = data.created_at;

                    const badge = document.getElementById('modalTypeBadge');
                    badge.className = `badge bg-${data.type_color}`;
                    badge.innerHTML =
                        `<i class="${data.type_icon} me-1"></i>${data.type.charAt(0).toUpperCase() + data.type.slice(1)}`;

                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('notificationDetailModal'));
                    modal.show();

                    // Refresh notifications list
                    loadNotifications();
                })
                .catch(err => {
                    console.error('Failed to load notification detail:', err);
                });
        }

        function markAllAsRead(event) {
            event.stopPropagation();

            fetch('{{ route('api.notifications.read-all') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                })
                .then(res => res.json())
                .then(() => {
                    updateBadge(0);
                    loadNotifications();
                });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Load notification count on page load
        document.addEventListener('DOMContentLoaded', function() {
            fetch('{{ route('api.notifications.fetch') }}?page=1', {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => updateBadge(data.unread_count))
                .catch(() => {});
        });
    </script>

    <style>
        .notification-dropdown {
            padding: 0;
        }

        .notification-dropdown-menu {
            width: 360px;
            max-height: 480px;
        }

        .notification-unread {
            background-color: rgba(var(--bs-primary-rgb), 0.05);
        }

        .notification-item:hover {
            background-color: rgba(0, 0, 0, 0.04);
        }

        .notification-item+.notification-item {
            border-top: 1px solid var(--bs-border-color-light);
        }

        .navbar-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            font-size: 0.6rem;
            min-width: 16px;
            height: 16px;
            line-height: 16px;
            text-align: center;
            padding: 0 4px;
            border-radius: 10px;
            background: var(--bs-danger);
            color: white;
            display: none;
        }

        .navbar-icon-btn {
            position: relative;
        }

        /* Small mobile: dropdown takes near-full width */
        @media (max-width: 575.98px) {
            .notification-dropdown-menu {
                position: fixed !important;
                top: 64px !important;
                right: 0.5rem !important;
                left: 0.5rem !important;
                width: auto !important;
                max-height: calc(100vh - 80px) !important;
                transform: none !important;
            }
        }

        /* Tablet: slightly smaller dropdown */
        @media (min-width: 576px) and (max-width: 991.98px) {
            .notification-dropdown-menu {
                width: 320px;
                max-height: 420px;
            }
        }
    </style>
@endpush

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
        {{-- Notifications (placeholder) --}}
        <button class="navbar-icon-btn" title="Notifications">
            <i class="bi bi-bell"></i>
            <span class="navbar-badge">0</span>
        </button>

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

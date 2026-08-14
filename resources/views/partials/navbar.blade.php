<nav class="navbar bg-white sticky-top border-bottom px-3 px-lg-4 py-2">
    <div class="d-flex align-items-center">
        {{-- Toggle sidebar (desktop + mobile) --}}
        <button class="btn btn-outline-secondary btn-sm me-3" onclick="toggleSidebar()" title="Toggle sidebar">
            <i class="bi bi-list fs-5"></i>
        </button>

        {{-- Breadcrumb / Page Title --}}
        <h5 class="mb-0 fw-semibold">@yield('title', 'Dashboard')</h5>
    </div>

    <div class="d-flex align-items-center gap-2">
        {{-- Notifications (placeholder) --}}
        <button class="btn btn-outline-secondary btn-sm position-relative" title="Notifications">
            <i class="bi bi-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                style="font-size: 0.6rem;">
                0
            </span>
        </button>

        {{-- User dropdown (desktop) --}}
        <div class="dropdown d-none d-md-block">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle d-flex align-items-center"
                data-bs-toggle="dropdown">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                    style="width: 28px; height: 28px; font-size: 0.75rem;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="d-none d-xl-inline">{{ auth()->user()->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') ?? '#' }}">
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
</nav>

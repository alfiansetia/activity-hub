<aside class="sidebar d-flex flex-column p-0">
    {{-- Brand --}}
    <div class="d-flex align-items-center justify-content-between px-3 py-3 border-bottom">
        <a href="{{ url('/dashboard') }}" class="d-flex align-items-center text-decoration-none">
            <i class="bi bi-activity text-primary fs-3 me-2"></i>
            <span class="fw-bold fs-5 text-dark">Activity Hub</span>
        </a>
        <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="toggleSidebar()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-grow-1 py-2 overflow-auto">
        <ul class="nav flex-column">
            {{-- Dashboard --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard') }}">
                    <i class="bi bi-grid-1x2 me-2"></i> Dashboard
                </a>
            </li>

            {{-- Activities --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('activities.*') ? 'active' : '' }}"
                    href="{{ route('activities.index') }}">
                    <i class="bi bi-calendar-check me-2"></i> Activities
                </a>
            </li>

            {{-- Create Activity (user/dosen only) --}}
            @if (auth()->user()->role !== 'admin')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('activities.create') ? 'active' : '' }}"
                        href="{{ route('activities.create') }}">
                        <i class="bi bi-plus-circle me-2"></i> Create Activity
                    </a>
                </li>
            @endif

            {{-- Profile --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                    href="{{ route('profile.edit') }}">
                    <i class="bi bi-person-gear me-2"></i> My Profile
                </a>
            </li>

            {{-- Admin / Dosen menus --}}
            @if (in_array(auth()->user()->role, ['admin', 'dosen']))
                <li class="nav-item mt-2 pt-2 border-top mx-3">
                    <small class="text-muted text-uppercase fw-semibold px-1 d-block mb-1"
                        style="font-size: 0.7rem;">Management</small>
                </li>

                @if (auth()->user()->role === 'admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                            href="{{ route('admin.users.index') }}">
                            <i class="bi bi-people me-2"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}"
                            href="{{ route('admin.companies.index') }}">
                            <i class="bi bi-building me-2"></i> Companies
                        </a>
                    </li>
                @endif
            @endif
        </ul>
    </nav>

    {{-- User Info --}}
    <div class="border-top px-3 py-3">
        <div class="d-flex align-items-center">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                style="width: 36px; height: 36px; font-size: 0.85rem;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="ms-2 flex-grow-1 overflow-hidden">
                <div class="fw-semibold text-truncate small">{{ auth()->user()->name }}</div>
                <small class="text-muted text-capitalize">{{ auth()->user()->role }}</small>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ms-1">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<aside class="sidebar">
    {{-- Brand --}}
    <div class="d-flex align-items-center justify-content-between" style="padding: 0;">
        <a href="{{ url('/dashboard') }}" class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="bi bi-activity"></i>
            </div>
            <div class="sidebar-brand-text">
                Activity Hub
                <small>Management System</small>
            </div>
        </a>
        <button class="sidebar-close me-3" onclick="toggleSidebar()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">
        <div class="sidebar-label">Main Menu</div>

        {{-- Dashboard --}}
        <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        {{-- Activities --}}
        <a class="sidebar-link {{ request()->routeIs('activities.index') || request()->routeIs('activities.show') ? 'active' : '' }}"
            href="{{ route('activities.index') }}">
            <i class="bi bi-calendar-check-fill"></i>
            <span>Activities</span>
        </a>

        {{-- Calendar --}}
        <a class="sidebar-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}"
            href="{{ route('calendar.index') }}">
            <i class="bi bi-calendar3"></i>
            <span>Calendar</span>
        </a>

        {{-- Create Activity (user only) --}}
        @if (auth()->user()->is_user)
            <a class="sidebar-link {{ request()->routeIs('activities.create') ? 'active' : '' }}"
                href="{{ route('activities.create') }}">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Create Activity</span>
            </a>
        @endif

        <div class="sidebar-divider"></div>

        <div class="sidebar-label">Account</div>

        {{-- Profile --}}
        <a class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
            href="{{ route('profile.edit') }}">
            <i class="bi bi-person-fill"></i>
            <span>My Profile</span>
        </a>

        {{-- Admin / Dosen menus --}}
        @if (in_array(auth()->user()->role, ['admin', 'dosen']))
            <div class="sidebar-divider"></div>
            <div class="sidebar-label">Administration</div>

            @if (auth()->user()->is_admin)
                <a class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                    href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Users</span>
                </a>
                <a class="sidebar-link {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}"
                    href="{{ route('admin.companies.index') }}">
                    <i class="bi bi-building-fill"></i>
                    <span>Companies</span>
                </a>
            @endif
        @endif
    </nav>

    {{-- User Info Footer --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role">{{ auth()->user()->role }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-logout-btn" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

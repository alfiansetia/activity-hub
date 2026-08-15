@extends('layouts.app')

@section('title', isset($companies) ? 'Activities - Select Company' : (isset($companyUsers) ? 'Activities - Select
    Student' : 'Activities'))

    @push('styles')
        <style>
            .company-card-link,
            .user-card-link {
                text-decoration: none;
                color: inherit;
                display: block;
                height: 100%;
            }

            .company-card,
            .user-card {
                background: var(--bg-card);
                border: 1px solid var(--border-light);
                border-radius: var(--radius-lg);
                box-shadow: var(--shadow-card);
                transition: all var(--transition-base);
                position: relative;
                overflow: hidden;
                height: 100%;
            }

            .company-card::before,
            .user-card::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                bottom: 0;
                width: 4px;
                background: var(--gradient-primary);
                opacity: 0.6;
                transition: opacity var(--transition-base);
            }

            .company-card::after,
            .user-card::after {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: var(--gradient-primary);
                opacity: 0;
                transition: opacity var(--transition-base);
            }

            .company-card:hover,
            .user-card:hover {
                transform: translateY(-4px);
                box-shadow: var(--shadow-lg);
                border-color: var(--primary-light);
            }

            .company-card:hover::before,
            .user-card:hover::before {
                opacity: 1;
            }

            .company-card:hover::after,
            .user-card:hover::after {
                opacity: 1;
            }

            .company-card .card-inner,
            .user-card .card-inner {
                padding: 1.25rem 1.25rem 1.25rem 1.5rem;
            }

            .company-icon-wrap,
            .user-avatar-wrap {
                width: 48px;
                height: 48px;
                border-radius: var(--radius-md);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.25rem;
                flex-shrink: 0;
                background: rgba(99, 102, 241, 0.1);
                color: var(--primary);
            }

            .user-avatar-wrap {
                font-size: 1.15rem;
                font-weight: 800;
            }

            .company-name,
            .user-name {
                font-size: 1rem;
                font-weight: 700;
                color: var(--text-primary);
                letter-spacing: -0.01em;
                margin-bottom: 0;
            }

            .company-total,
            .user-email {
                font-size: 0.78rem;
                color: var(--text-muted);
                font-weight: 500;
            }

            .company-stat-mini,
            .user-stat-mini {
                display: inline-flex;
                align-items: center;
                gap: 0.3rem;
                font-size: 0.72rem;
                font-weight: 600;
                padding: 0.3rem 0.6rem;
                border-radius: var(--radius-full);
            }

            .company-arrow,
            .user-arrow {
                opacity: 0;
                transform: translateX(-8px);
                transition: all var(--transition-base);
                color: var(--primary);
                font-size: 1.1rem;
            }

            .company-card-link:hover .company-arrow,
            .user-card-link:hover .user-arrow {
                opacity: 1;
                transform: translateX(0);
            }

            .page-hero {
                background: var(--gradient-card);
                border: 1px solid var(--border-light);
                border-radius: var(--radius-lg);
                padding: 1.5rem;
                margin-bottom: 1.5rem;
                box-shadow: var(--shadow-sm);
                position: relative;
                overflow: hidden;
            }

            .page-hero::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                bottom: 0;
                width: 4px;
                background: var(--gradient-primary);
            }

            .page-hero-title {
                font-size: 1.35rem;
                font-weight: 800;
                color: var(--text-primary);
                letter-spacing: -0.02em;
                margin-bottom: 0.15rem;
            }

            .page-hero-subtitle {
                font-size: 0.85rem;
                color: var(--text-muted);
                font-weight: 500;
                margin-bottom: 0;
            }
        </style>
    @endpush

@section('content')
    {{-- ===================== DOSEN: COMPANY GRID ===================== --}}
    @if (isset($companies))
        {{-- Breadcrumb --}}
        @include('partials.breadcrumb', [
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'bi bi-house-door'],
                ['label' => 'Activities', 'icon' => 'bi bi-calendar-check'],
            ],
        ])

        {{-- Page Hero Header --}}
        <div class="page-hero fade-up d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="page-hero-title">
                    <i class="bi bi-building me-2" style="color: var(--primary);"></i>Select Company
                </h4>
                <p class="page-hero-subtitle">Choose a company to view its students and activities.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge badge-soft-primary" style="font-size: 0.8rem; padding: 0.45em 0.85em;">
                    <i class="bi bi-buildings me-1"></i> {{ $companies->count() }}
                    {{ Str::plural('Company', $companies->count()) }}
                </span>
            </div>
        </div>

        @if ($companies->isEmpty())
            <div class="card fade-up fade-up-delay-1">
                <div class="card-body text-center py-5">
                    <div
                        style="width: 64px; height: 64px; border-radius: var(--radius-lg); background: var(--bg-body); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem; color: var(--text-muted);">
                        <i class="bi bi-building"></i>
                    </div>
                    <h6 class="fw-bold text-muted mb-1">No Companies Available</h6>
                    <p class="text-muted small mb-0">There are no companies in the system yet.</p>
                </div>
            </div>
        @else
            <div class="row g-3">
                @foreach ($companies as $company)
                    <div class="col-sm-6 col-lg-4 col-xl-3 fade-up fade-up-delay-{{ min($loop->iteration, 4) }}">
                        <a href="{{ route('activities.index', ['company_id' => $company->id]) }}" class="company-card-link">
                            <div class="company-card">
                                <div class="card-inner">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div class="company-icon-wrap">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <span class="company-arrow">
                                            <i class="bi bi-arrow-right-short"></i>
                                        </span>
                                    </div>
                                    <h6 class="company-name">{{ $company->name }}</h6>
                                    <p class="company-total mb-3">
                                        {{ $company->activities_count }}
                                        {{ Str::plural('activity', $company->activities_count) }} total
                                    </p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="company-stat-mini badge-soft-warning">
                                            <i class="bi bi-hourglass-split"></i> {{ $company->pending_count }}
                                        </span>
                                        <span class="company-stat-mini badge-soft-success">
                                            <i class="bi bi-check-circle"></i> {{ $company->accept_count }}
                                        </span>
                                        <span class="company-stat-mini badge-soft-danger">
                                            <i class="bi bi-x-circle"></i> {{ $company->reject_count }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ===================== DOSEN: USERS IN COMPANY ===================== --}}
    @elseif (isset($companyUsers))
        {{-- Breadcrumb --}}
        @include('partials.breadcrumb', [
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'bi bi-house-door'],
                ['label' => 'Activities', 'url' => route('activities.index'), 'icon' => 'bi bi-calendar-check'],
                ['label' => $selectedCompany->name],
            ],
        ])

        {{-- Page Hero Header --}}
        <div class="page-hero fade-up d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="page-hero-title">
                    <a href="{{ route('activities.index') }}" class="text-decoration-none me-2"
                        style="color: var(--primary);" title="Back to companies">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    {{ $selectedCompany->name }} — Students
                </h4>
                <p class="page-hero-subtitle">
                    Select a student to view their activities.
                    <a href="{{ route('activities.index') }}" class="text-decoration-none"
                        style="color: var(--primary); font-weight: 600;">
                        <i class="bi bi-grid-3x3-gap me-1"></i>Back to companies
                    </a>
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge badge-soft-primary" style="font-size: 0.8rem; padding: 0.45em 0.85em;">
                    <i class="bi bi-person-fill me-1"></i> {{ $companyUsers->total() }}
                    {{ Str::plural('Student', $companyUsers->total()) }}
                </span>
            </div>
        </div>

        {{-- Search --}}
        <div class="card mb-4 fade-up fade-up-delay-1">
            <div class="card-body py-2">
                <form method="GET" class="row g-2 align-items-end">
                    <input type="hidden" name="company_id" value="{{ $selectedCompany->id }}">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Search students..." value="{{ request('search') }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-search me-1"></i> Search
                        </button>
                        @if (request('search'))
                            <a href="{{ route('activities.index', ['company_id' => $selectedCompany->id]) }}"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if ($companyUsers->isEmpty())
            <div class="card fade-up fade-up-delay-1">
                <div class="card-body text-center py-5">
                    <div
                        style="width: 64px; height: 64px; border-radius: var(--radius-lg); background: var(--bg-body); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem; color: var(--text-muted);">
                        <i class="bi bi-people"></i>
                    </div>
                    <h6 class="fw-bold text-muted mb-1">No Students Found</h6>
                    <p class="text-muted small mb-0">
                        @if (request('search'))
                            No students match your search "{{ request('search') }}".
                        @else
                            There are no approved students in this company yet.
                        @endif
                    </p>
                </div>
            </div>
        @else
            <div class="row g-3">
                @foreach ($companyUsers as $companyUser)
                    <div class="col-sm-6 col-lg-4 col-xl-3 fade-up fade-up-delay-{{ min($loop->iteration, 4) }}">
                        <a href="{{ route('activities.index', ['company_id' => $selectedCompany->id, 'user_id' => $companyUser->id]) }}"
                            class="user-card-link">
                            <div class="user-card">
                                <div class="card-inner">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div class="user-avatar-wrap">
                                            {{ strtoupper(substr($companyUser->name, 0, 1)) }}
                                        </div>
                                        <span class="user-arrow">
                                            <i class="bi bi-arrow-right-short"></i>
                                        </span>
                                    </div>
                                    <h6 class="user-name">{{ $companyUser->name }}</h6>
                                    <p class="user-email mb-3">
                                        {{ $companyUser->email }}
                                    </p>
                                    <p class="mb-2"
                                        style="font-size: 0.78rem; color: var(--text-muted); font-weight: 500;">
                                        {{ $companyUser->activities_count }}
                                        {{ Str::plural('activity', $companyUser->activities_count) }} total
                                    </p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="user-stat-mini badge-soft-warning">
                                            <i class="bi bi-hourglass-split"></i> {{ $companyUser->pending_count }}
                                        </span>
                                        <span class="user-stat-mini badge-soft-success">
                                            <i class="bi bi-check-circle"></i> {{ $companyUser->accept_count }}
                                        </span>
                                        <span class="user-stat-mini badge-soft-danger">
                                            <i class="bi bi-x-circle"></i> {{ $companyUser->reject_count }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4 fade-up">
                {{ $companyUsers->links() }}
            </div>
        @endif

        {{-- ===================== ACTIVITY LIST (all roles) ===================== --}}
    @else
        {{-- Breadcrumb --}}
        @if (isset($selectedUser) && isset($selectedCompany))
            @include('partials.breadcrumb', [
                'breadcrumbs' => [
                    ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'bi bi-house-door'],
                    [
                        'label' => 'Activities',
                        'url' => route('activities.index'),
                        'icon' => 'bi bi-calendar-check',
                    ],
                    [
                        'label' => $selectedCompany->name,
                        'url' => route('activities.index', ['company_id' => $selectedCompany->id]),
                    ],
                    ['label' => $selectedUser->name],
                ],
            ])
        @elseif (isset($selectedCompany))
            @include('partials.breadcrumb', [
                'breadcrumbs' => [
                    ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'bi bi-house-door'],
                    [
                        'label' => 'Activities',
                        'url' => route('activities.index'),
                        'icon' => 'bi bi-calendar-check',
                    ],
                    ['label' => $selectedCompany->name],
                ],
            ])
        @else
            @include('partials.breadcrumb', [
                'breadcrumbs' => [
                    ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'bi bi-house-door'],
                    ['label' => 'Activities'],
                ],
            ])
        @endif

        {{-- Page Hero Header --}}
        <div class="page-hero fade-up d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="page-hero-title">
                    @if (isset($selectedUser) && isset($selectedCompany))
                        <a href="{{ route('activities.index', ['company_id' => $selectedCompany->id]) }}"
                            class="text-decoration-none me-2" style="color: var(--primary);" title="Back to students">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        {{ $selectedUser->name }}
                    @elseif (isset($selectedCompany))
                        <a href="{{ route('activities.index') }}" class="text-decoration-none me-2"
                            style="color: var(--primary);" title="Back to companies">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        {{ $selectedCompany->name }}
                    @else
                        <i class="bi bi-calendar-check me-2" style="color: var(--primary);"></i>Activities
                    @endif
                </h4>
                <p class="page-hero-subtitle">
                    @if (isset($selectedUser) && isset($selectedCompany))
                        Viewing activities for <strong>{{ $selectedUser->name }}</strong> in
                        <strong>{{ $selectedCompany->name }}</strong>.
                        <a href="{{ route('activities.index', ['company_id' => $selectedCompany->id]) }}"
                            class="text-decoration-none" style="color: var(--primary); font-weight: 600;">
                            <i class="bi bi-people me-1"></i>Back to students
                        </a>
                    @elseif (isset($selectedCompany))
                        Viewing all activities for <strong>{{ $selectedCompany->name }}</strong>.
                        <a href="{{ route('activities.index') }}" class="text-decoration-none"
                            style="color: var(--primary); font-weight: 600;">
                            <i class="bi bi-grid-3x3-gap me-1"></i>Back to companies
                        </a>
                    @else
                        Manage and track all activities.
                    @endif
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if (auth()->user()->is_user)
                    <a href="{{ route('activities.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> New Activity
                    </a>
                @endif
            </div>
        </div>

        {{-- Filters --}}
        <div class="card mb-4 fade-up fade-up-delay-1">
            <div class="card-body py-2">
                <form method="GET" class="row g-2 align-items-end">
                    @if (isset($selectedCompany))
                        <input type="hidden" name="company_id" value="{{ $selectedCompany->id }}">
                    @endif
                    @if (isset($selectedUser))
                        <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">
                    @endif
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Search by title..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="accept" {{ request('status') === 'accept' ? 'selected' : '' }}>Accepted
                            </option>
                            <option value="reject" {{ request('status') === 'reject' ? 'selected' : '' }}>Rejected
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-search me-1"></i> Filter
                        </button>
                        @if (isset($selectedUser) && isset($selectedCompany))
                            <a href="{{ route('activities.index', ['company_id' => $selectedCompany->id, 'user_id' => $selectedUser->id]) }}"
                                class="btn btn-sm btn-outline-secondary">Reset</a>
                        @elseif (isset($selectedCompany))
                            <a href="{{ route('activities.index', ['company_id' => $selectedCompany->id]) }}"
                                class="btn btn-sm btn-outline-secondary">Reset</a>
                        @else
                            <a href="{{ route('activities.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Activity List --}}
        <div class="card fade-up fade-up-delay-2">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-list-ul me-2" style="color: var(--primary);"></i>
                    @if (isset($selectedUser) && isset($selectedCompany))
                        {{ $selectedUser->name }}'s Activities
                    @elseif (isset($selectedCompany))
                        {{ $selectedCompany->name }} Activities
                    @else
                        All Activities
                    @endif
                </h6>
                @if (isset($activities))
                    <span class="badge badge-soft-primary">{{ $activities->total() }}
                        {{ Str::plural('result', $activities->total()) }}</span>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                @if (!isset($selectedCompany))
                                    <th class="d-none d-md-table-cell">Company</th>
                                @endif
                                @if (!isset($selectedUser))
                                    <th class="d-none d-md-table-cell">Created By</th>
                                @endif
                                <th>Status</th>
                                <th class="d-none d-sm-table-cell">Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activities as $activity)
                                <tr>
                                    <td>
                                        <a href="{{ route('activities.show', $activity) }}"
                                            class="fw-semibold text-decoration-none" style="color: var(--text-primary);">
                                            {{ Str::limit($activity->title, 40) }}
                                        </a>
                                        @if (!isset($selectedCompany))
                                            <div class="d-md-none">
                                                <small
                                                    style="color: var(--text-muted);">{{ $activity->company->name ?? '-' }}</small>
                                            </div>
                                        @endif
                                    </td>
                                    @if (!isset($selectedCompany))
                                        <td class="d-none d-md-table-cell" style="color: var(--text-secondary);">
                                            {{ $activity->company->name ?? '-' }}</td>
                                    @endif
                                    @if (!isset($selectedUser))
                                        <td class="d-none d-md-table-cell"
                                            style="color: var(--text-muted); font-size: 0.85rem;">
                                            {{ $activity->user->name ?? '-' }}</td>
                                    @endif
                                    <td>
                                        @if ($activity->is_accept)
                                            <span class="badge badge-soft-success"><i
                                                    class="bi bi-check-circle me-1"></i>Accepted</span>
                                        @elseif ($activity->is_reject)
                                            <span class="badge badge-soft-danger"><i
                                                    class="bi bi-x-circle me-1"></i>Rejected</span>
                                        @else
                                            <span class="badge badge-soft-warning"><i
                                                    class="bi bi-hourglass-split me-1"></i>Pending</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-sm-table-cell"
                                        style="color: var(--text-muted); font-size: 0.85rem;">
                                        {{ $activity->date?->format('d M Y') ?? '-' }}
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('activities.show', $activity) }}"
                                                class="btn btn-outline-secondary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if (
                                                !$activity->is_accept &&
                                                    (auth()->user()->is_admin ||
                                                        $activity->user_id === auth()->id() ||
                                                        $activity->company_id === auth()->user()->company_id))
                                                <a href="{{ route('activities.edit', $activity) }}"
                                                    class="btn btn-outline-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div
                                            style="width: 56px; height: 56px; border-radius: var(--radius-lg); background: var(--bg-body); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; font-size: 1.35rem; color: var(--text-muted);">
                                            <i class="bi bi-inbox"></i>
                                        </div>
                                        <h6 class="fw-bold text-muted mb-1">No Activities Found</h6>
                                        <p class="text-muted small mb-0">
                                            @if (request('search') || request('status'))
                                                Try adjusting your filters.
                                            @else
                                                There are no activities yet.
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if (isset($activities) && $activities->hasPages())
                <div class="card-footer bg-white">
                    {{ $activities->links() }}
                </div>
            @endif
        </div>
    @endif
@endsection

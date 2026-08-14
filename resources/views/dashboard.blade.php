@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    {{-- Welcome --}}
    <div class="mb-4 fade-up">
        <h4 class="fw-bold mb-1" style="letter-spacing: -0.02em;">
            Welcome back, <span class="text-gradient">{{ auth()->user()->name }}</span>! 👋
        </h4>
        <p class="mb-0" style="color: var(--text-muted);">Here's an overview of your activity system.</p>
    </div>

    {{-- Company Join Request (no company assigned) --}}
    @if (!auth()->user()->company_id && auth()->user()->is_user)
        <div class="fade-up mb-4">
            <div class="card" style="border: 2px dashed var(--primary); border-radius: 0.75rem;">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3">
                        <div style="font-size: 1.5rem; color: var(--primary); line-height: 1;">
                            <i class="bi bi-building-add"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1">Join a Company</h6>
                            <p class="mb-3" style="font-size: 0.875rem; color: var(--text-secondary);">
                                You need to join a company before you can create activities. Select a company below and
                                submit your request.
                            </p>
                            <form method="POST" action="{{ route('profile.company-request') }}"
                                class="d-flex gap-2 align-items-end">
                                @csrf
                                <div class="flex-grow-1">
                                    <select name="company_id" class="form-select" required>
                                        <option value="">-- Select Company --</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary text-nowrap">
                                    <i class="bi bi-send me-1"></i> Submit Request
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Company Status Alert (pending/rejected) --}}
    @if (auth()->user()->company_id && auth()->user()->company_status !== 'accept')
        <div class="fade-up mb-4">
            @if (auth()->user()->company_status === 'reject')
                <div class="alert d-flex align-items-start gap-3 mb-0" role="alert"
                    style="border-radius: 0.75rem; border: none; background: rgba(239,68,68,0.08); color: var(--danger);">
                    <div style="font-size: 1.25rem; line-height: 1;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1" style="color: var(--danger);">Company Access Rejected</h6>
                        <p class="mb-1" style="font-size: 0.875rem; color: var(--text-secondary);">
                            Your company join request has been rejected. You cannot create activities until approved.
                        </p>
                        @if (auth()->user()->company_reject_reason)
                            <div class="mt-2 p-2 rounded"
                                style="background: rgba(239,68,68,0.06); font-size: 0.85rem; color: var(--text-primary);">
                                <strong>Reason:</strong> {{ auth()->user()->company_reject_reason }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('profile.company-request') }}"
                            class="mt-3 d-flex gap-2 align-items-end">
                            @csrf
                            <select name="company_id" class="form-select form-select-sm" style="max-width: 250px;" required>
                                <option value="">-- Select Company --</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}"
                                        {{ $company->id == auth()->user()->company_id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-primary text-nowrap">
                                <i class="bi bi-arrow-repeat me-1"></i> Re-submit Request
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert d-flex align-items-start gap-3 mb-0" role="alert"
                    style="border-radius: 0.75rem; border: none; background: rgba(245,158,11,0.08); color: var(--warning);">
                    <div style="font-size: 1.25rem; line-height: 1;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1" style="color: var(--warning);">Pending Approval</h6>
                        <p class="mb-0" style="font-size: 0.875rem; color: var(--text-secondary);">
                            Your company join request is pending approval. You cannot create activities until approved by an
                            administrator.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3 fade-up">
            <div class="card-stat h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon primary me-3">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>
                        <div>
                            <div class="stat-label">Total Activities</div>
                            <div class="stat-value">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 fade-up fade-up-delay-1">
            <div class="card-stat h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon warning me-3">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div>
                            <div class="stat-label">Pending</div>
                            <div class="stat-value">{{ $stats['pending'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 fade-up fade-up-delay-2">
            <div class="card-stat h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon success me-3">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div>
                            <div class="stat-label">Accepted</div>
                            <div class="stat-value">{{ $stats['accept'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 fade-up fade-up-delay-3">
            <div class="card-stat h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon danger me-3">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <div>
                            <div class="stat-label">Rejected</div>
                            <div class="stat-value">{{ $stats['reject'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="card fade-up fade-up-delay-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold">
                <i class="bi bi-clock-history me-2" style="color: var(--primary);"></i>Recent Activities
            </h6>
            <a href="{{ route('activities.index') }}" class="btn btn-sm btn-outline-primary">
                View All <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($recentActivities ?? []) as $activity)
                            <tr>
                                <td class="fw-semibold">{{ $activity->title }}</td>
                                <td style="color: var(--text-muted);">{{ $activity->company->name ?? '-' }}</td>
                                <td>
                                    @if ($activity->status === 'accept')
                                        <span class="badge badge-soft-success">Accepted</span>
                                    @elseif($activity->status === 'reject')
                                        <span class="badge badge-soft-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-soft-warning">Pending</span>
                                    @endif
                                </td>
                                <td style="color: var(--text-muted); font-size: 0.825rem;">
                                    {{ $activity->date?->format('d M Y') ?? '-' }}
                                </td>
                                <td>
                                    <a href="{{ route('activities.show', $activity) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5" style="color: var(--text-muted);">
                                    <i class="bi bi-inbox fs-2 d-block mb-2" style="opacity: 0.4;"></i>
                                    No activities yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

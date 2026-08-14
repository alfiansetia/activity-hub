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

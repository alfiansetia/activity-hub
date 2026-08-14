@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    {{-- Welcome --}}
    <div class="mb-4">
        <h4 class="fw-semibold mb-1">Welcome back, {{ auth()->user()->name }}!</h4>
        <p class="text-muted mb-0">Here's an overview of your activity system.</p>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stat h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center me-3"
                            style="width: 48px; height: 48px;">
                            <i class="bi bi-calendar-check fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Activities</div>
                            <div class="fs-4 fw-bold">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stat h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center me-3"
                            style="width: 48px; height: 48px;">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Pending</div>
                            <div class="fs-4 fw-bold">{{ $stats['pending'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stat h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center me-3"
                            style="width: 48px; height: 48px;">
                            <i class="bi bi-check-circle fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Accepted</div>
                            <div class="fs-4 fw-bold">{{ $stats['accept'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stat h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-3 d-flex align-items-center justify-content-center me-3"
                            style="width: 48px; height: 48px;">
                            <i class="bi bi-x-circle fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Rejected</div>
                            <div class="fs-4 fw-bold">{{ $stats['reject'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="card">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-semibold">
                <i class="bi bi-clock-history me-2"></i> Recent Activities
            </h6>
            <a href="{{ route('activities.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
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
                                <td>{{ $activity->company->name ?? '-' }}</td>
                                <td>
                                    @if ($activity->status === 'accept')
                                        <span class="badge bg-success">Accepted</span>
                                    @elseif($activity->status === 'reject')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $activity->date?->format('d M Y') ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('activities.show', $activity) }}"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
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

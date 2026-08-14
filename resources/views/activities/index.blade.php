@extends('layouts.app')

@section('title', 'Activities')

@section('content')
    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h4 class="fw-semibold mb-1">Activities</h4>
            <p class="text-muted small mb-0">Manage and track all activities.</p>
        </div>
        @if (auth()->user()->role !== 'admin' || true)
            <a href="{{ route('activities.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> New Activity
            </a>
        @endif
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Search by title..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accept" {{ request('status') === 'accept' ? 'selected' : '' }}>Accepted</option>
                        <option value="reject" {{ request('status') === 'reject' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-outline-primary me-1">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('activities.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Activity List --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th class="d-none d-md-table-cell">Company</th>
                            <th class="d-none d-md-table-cell">Created By</th>
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
                                        class="fw-semibold text-decoration-none">
                                        {{ Str::limit($activity->title, 40) }}
                                    </a>
                                    <div class="d-md-none">
                                        <small class="text-muted">{{ $activity->company->name ?? '-' }}</small>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">{{ $activity->company->name ?? '-' }}</td>
                                <td class="d-none d-md-table-cell text-muted small">{{ $activity->user->name ?? '-' }}</td>
                                <td>
                                    @if ($activity->status === 'accept')
                                        <span class="badge bg-success"><i
                                                class="bi bi-check-circle me-1"></i>Accepted</span>
                                    @elseif ($activity->status === 'reject')
                                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                                    @else
                                        <span class="badge bg-warning text-dark"><i
                                                class="bi bi-hourglass-split me-1"></i>Pending</span>
                                    @endif
                                </td>
                                <td class="d-none d-sm-table-cell text-muted small">
                                    {{ $activity->date?->format('d M Y') ?? '-' }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('activities.show', $activity) }}"
                                            class="btn btn-outline-secondary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if (
                                            $activity->status !== 'accept' &&
                                                (auth()->user()->role === 'admin' ||
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
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    No activities found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($activities->hasPages())
            <div class="card-footer bg-white">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
@endsection

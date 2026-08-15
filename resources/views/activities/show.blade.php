@extends('layouts.app')

@section('title', $activity->title)

@section('content')
    {{-- Breadcrumb --}}
    @if (auth()->user()->is_dosen)
        {{-- Dosen: Dashboard → Activities → Company → User → Title --}}
        @include('partials.breadcrumb', [
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'bi bi-house-door'],
                ['label' => 'Activities', 'url' => route('activities.index'), 'icon' => 'bi bi-calendar-check'],
                [
                    'label' => $activity->company->name ?? 'Company',
                    'url' => route('activities.index', ['company_id' => $activity->company_id]),
                ],
                [
                    'label' => $activity->user->name ?? 'Student',
                    'url' => route('activities.index', [
                        'company_id' => $activity->company_id,
                        'user_id' => $activity->user_id,
                    ]),
                ],
                ['label' => Str::limit($activity->title, 30)],
            ],
        ])
    @elseif (request('company_id'))
        @include('partials.breadcrumb', [
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'bi bi-house-door'],
                ['label' => 'Activities', 'url' => route('activities.index'), 'icon' => 'bi bi-calendar-check'],
                [
                    'label' => $activity->company->name ?? 'Company',
                    'url' => route('activities.index', ['company_id' => $activity->company_id]),
                ],
                ['label' => Str::limit($activity->title, 30)],
            ],
        ])
    @else
        @include('partials.breadcrumb', [
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'bi bi-house-door'],
                ['label' => 'Activities', 'url' => route('activities.index'), 'icon' => 'bi bi-calendar-check'],
                ['label' => Str::limit($activity->title, 30)],
            ],
        ])
    @endif

    {{-- Actions Bar --}}
    <div class="d-flex flex-wrap align-items-center gap-2 mb-4 fade-up">
        <div class="ms-auto d-flex flex-wrap gap-2">
            {{-- Preview HTML button --}}
            <a href="{{ route('activities.preview', $activity) }}" class="btn btn-outline-info btn-sm" target="_blank">
                <i class="bi bi-eye me-1"></i> Preview Report
            </a>

            {{-- Download PDF button --}}
            <a href="{{ route('activities.pdf', $activity) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
            </a>

            {{-- Edit button --}}
            @if (
                !$activity->is_accept &&
                    (auth()->user()->is_admin ||
                        $activity->user_id === auth()->id() ||
                        $activity->company_id === auth()->user()->company_id))
                <a href="{{ route('activities.edit', $activity) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil-fill me-1"></i> Edit
                </a>
            @endif

            {{-- Delete (admin: any status, owner: pending only) --}}
            @if (auth()->user()->is_admin || ($activity->user_id === auth()->id() && $activity->is_pending))
                <form method="POST" action="{{ route('activities.destroy', $activity) }}"
                    onsubmit="return confirm('Delete this activity?')" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-trash-fill me-1"></i> Delete
                    </button>
                </form>
            @endif

            {{-- Dosen: Accept / Reject --}}
            @if (auth()->user()->is_dosen && $activity->is_pending)
                <button type="button" class="btn btn-sm"
                    style="background: var(--success); color: #fff; font-weight: 600; box-shadow: 0 2px 8px rgba(34,197,94,0.3);"
                    data-bs-toggle="modal" data-bs-target="#acceptModal">
                    <i class="bi bi-check-lg me-1"></i> Accept
                </button>
                <button type="button" class="btn btn-sm"
                    style="background: var(--danger); color: #fff; font-weight: 600; box-shadow: 0 2px 8px rgba(239,68,68,0.3);"
                    data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-lg me-1"></i> Reject
                </button>
            @endif
        </div>
    </div>

    {{-- Hero Header Card --}}
    <div class="activity-show-header fade-up fade-up-delay-1">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
            <div class="flex-grow-1">
                <h2 class="activity-show-title">{{ $activity->title }}</h2>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="activity-meta-pill">
                        <i class="bi bi-building-fill"></i> {{ $activity->company->name ?? '-' }}
                    </span>
                    <span class="activity-meta-pill">
                        <i class="bi bi-person-fill"></i> {{ $activity->user->name ?? '-' }}
                    </span>
                    <span class="activity-meta-pill">
                        <i class="bi bi-calendar3"></i> {{ $activity->date?->format('d M Y, H:i') ?? '-' }}
                    </span>
                    <span class="activity-meta-pill">
                        <i class="bi bi-hash"></i> {{ $activity->id }}
                    </span>
                </div>
            </div>
            <div class="flex-shrink-0">
                @if ($activity->is_accept)
                    <span class="activity-status-badge success">
                        <i class="bi bi-check-circle-fill"></i> Accepted
                    </span>
                @elseif ($activity->is_reject)
                    <span class="activity-status-badge danger">
                        <i class="bi bi-x-circle-fill"></i> Rejected
                    </span>
                @else
                    <span class="activity-status-badge warning">
                        <i class="bi bi-hourglass-split"></i> Pending
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Description --}}
            @if ($activity->descriptions)
                <div class="card mb-4 fade-up fade-up-delay-2">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-text-left me-2" style="color: var(--primary);"></i> Description
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="detail-content">{{ $activity->descriptions }}</div>
                    </div>
                </div>
            @endif

            {{-- Rules --}}
            @if ($activity->rules)
                <div class="card mb-4 fade-up fade-up-delay-2">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-list-check me-2" style="color: var(--warning);"></i> Rules
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="detail-content">{{ $activity->rules }}</div>
                    </div>
                </div>
            @endif

            {{-- Tools --}}
            @if ($activity->tools)
                <div class="card mb-4 fade-up fade-up-delay-3">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-tools me-2" style="color: var(--info);"></i> Tools
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="detail-content">{{ $activity->tools }}</div>
                    </div>
                </div>
            @endif

            {{-- Additional Location --}}
            @if ($activity->additional_location)
                <div class="card mb-4 fade-up fade-up-delay-3">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-geo-alt me-2" style="color: var(--danger);"></i> Additional Location
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="detail-content">{{ $activity->additional_location }}</div>
                    </div>
                </div>
            @endif

            {{-- Tests --}}
            @if ($activity->tests)
                <div class="card mb-4 fade-up fade-up-delay-3">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-clipboard-check me-2" style="color: var(--success);"></i> Tests
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="detail-content">{{ $activity->tests }}</div>
                    </div>
                </div>
            @endif

            {{-- Ulasan --}}
            @if ($activity->ulasan)
                <div class="card mb-4 fade-up fade-up-delay-3">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-chat-text me-2" style="color: var(--primary);"></i> Ulasan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="detail-content">{{ $activity->ulasan }}</div>
                    </div>
                </div>
            @endif

            {{-- Attachments --}}
            @if ($activity->attachments->count())
                <div class="card fade-up fade-up-delay-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-images me-2" style="color: var(--success);"></i> Attachments
                        </h6>
                        <span class="badge badge-soft-primary">
                            {{ $activity->attachments->count() }}
                            {{ Str::plural('image', $activity->attachments->count()) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 attachment-grid">
                            @foreach ($activity->attachments as $att)
                                <div class="col-sm-6 col-md-4">
                                    <div class="attachment-card" data-bs-toggle="modal"
                                        data-bs-target="#lightbox{{ $att->id }}">
                                        <div class="attachment-img-wrapper">
                                            <img src="{{ Storage::url($att->image_url) }}"
                                                alt="{{ $att->caption ?: 'Attachment' }}">
                                            <div class="attachment-overlay">
                                                <i class="bi bi-arrows-fullscreen"></i>
                                            </div>
                                        </div>
                                        <div class="attachment-caption">
                                            <i class="bi bi-chat-quote me-1"></i>
                                            {{ $att->caption ?: 'No caption' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Lightbox Modals --}}
            @foreach ($activity->attachments as $att)
                <div class="modal fade lightbox-modal" id="lightbox{{ $att->id }}" tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content lightbox-content">
                            <div class="modal-header lightbox-header">
                                <h6 class="modal-title" style="color: #fff; font-size: 0.9rem;">
                                    <i class="bi bi-image me-2"></i>{{ $att->caption ?: 'Attachment' }}
                                </h6>
                                <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body lightbox-body">
                                <img src="{{ Storage::url($att->image_url) }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Rejection Info --}}
            @if ($activity->is_reject && $activity->reject_reason)
                <div class="card mb-4 fade-up fade-up-delay-2 activity-alert-card danger">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3">
                            <div class="activity-alert-icon danger">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1" style="color: var(--danger);">Rejection Reason</h6>
                                <p class="mb-2"
                                    style="font-size: 0.875rem; color: var(--text-secondary); line-height: 1.6;">
                                    {{ $activity->reject_reason }}
                                </p>
                                @if ($activity->rejector)
                                    <small style="color: var(--text-muted);">
                                        <i class="bi bi-person-fill me-1"></i> {{ $activity->rejector->name }}
                                        &middot; {{ $activity->reject_at?->format('d M Y, H:i') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Acceptance Info --}}
            @if ($activity->is_accept)
                <div class="card mb-4 fade-up fade-up-delay-2 activity-alert-card success">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3">
                            <div class="activity-alert-icon success">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1" style="color: var(--success);">Activity Accepted</h6>
                                @if ($activity->dosen_note)
                                    <p class="mb-2"
                                        style="font-size: 0.875rem; color: var(--text-secondary); line-height: 1.6;">
                                        <i class="bi bi-chat-quote me-1"></i> {{ $activity->dosen_note }}
                                    </p>
                                @endif
                                @if ($activity->acceptor)
                                    <small style="color: var(--text-muted);">
                                        <i class="bi bi-person-fill me-1"></i> {{ $activity->acceptor->name }}
                                        &middot; {{ $activity->accept_at?->format('d M Y, H:i') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Activity Info Card --}}
            <div class="card mb-4 fade-up fade-up-delay-3">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-info-circle-fill me-2" style="color: var(--primary);"></i> Activity Details
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="detail-info-list">
                        <div class="detail-info-item">
                            <div class="detail-info-icon primary">
                                <i class="bi bi-hash"></i>
                            </div>
                            <div>
                                <div class="detail-info-label">Activity ID</div>
                                <div class="detail-info-value">#{{ $activity->id }}</div>
                            </div>
                        </div>
                        <div class="detail-info-item">
                            <div class="detail-info-icon info">
                                <i class="bi bi-building-fill"></i>
                            </div>
                            <div>
                                <div class="detail-info-label">Company</div>
                                <div class="detail-info-value">{{ $activity->company->name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="detail-info-item">
                            <div class="detail-info-icon warning">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div>
                                <div class="detail-info-label">Created By</div>
                                <div class="detail-info-value">{{ $activity->user->name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="detail-info-item">
                            <div class="detail-info-icon success">
                                <i class="bi bi-calendar3"></i>
                            </div>
                            <div>
                                <div class="detail-info-label">Date</div>
                                <div class="detail-info-value">{{ $activity->date?->format('d M Y, H:i') ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Timeline Card --}}
            <div class="card fade-up fade-up-delay-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-clock-history me-2" style="color: var(--text-muted);"></i> Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="activity-timeline">
                        <div class="timeline-item">
                            <div class="timeline-dot"
                                style="background: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.2);"></div>
                            <div class="timeline-content">
                                <div class="timeline-label">Created</div>
                                <div class="timeline-desc">
                                    {{ $activity->created_at?->format('d M Y, H:i') }}
                                </div>
                            </div>
                        </div>

                        @if ($activity->re_submit_at)
                            <div class="timeline-item">
                                <div class="timeline-dot"
                                    style="background: var(--info); box-shadow: 0 0 0 3px rgba(6,182,212,0.2);"></div>
                                <div class="timeline-content">
                                    <div class="timeline-label">Resubmitted</div>
                                    <div class="timeline-desc">
                                        {{ $activity->re_submit_at?->format('d M Y, H:i') }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($activity->is_accept)
                            <div class="timeline-item">
                                <div class="timeline-dot"
                                    style="background: var(--success); box-shadow: 0 0 0 3px rgba(34,197,94,0.2);"></div>
                                <div class="timeline-content">
                                    <div class="timeline-label">Accepted</div>
                                    <div class="timeline-desc">
                                        {{ $activity->accept_at?->format('d M Y, H:i') }}
                                        @if ($activity->acceptor)
                                            &middot; {{ $activity->acceptor->name }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @elseif ($activity->is_reject)
                            <div class="timeline-item">
                                <div class="timeline-dot"
                                    style="background: var(--danger); box-shadow: 0 0 0 3px rgba(239,68,68,0.2);"></div>
                                <div class="timeline-content">
                                    <div class="timeline-label">Rejected</div>
                                    <div class="timeline-desc">
                                        {{ $activity->reject_at?->format('d M Y, H:i') }}
                                        @if ($activity->rejector)
                                            &middot; {{ $activity->rejector->name }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="timeline-item">
                                <div class="timeline-dot"
                                    style="background: var(--warning); box-shadow: 0 0 0 3px rgba(245,158,11,0.2);"></div>
                                <div class="timeline-content">
                                    <div class="timeline-label">Pending Review</div>
                                    <div class="timeline-desc">Awaiting approval</div>
                                </div>
                            </div>
                        @endif

                        <div class="timeline-item last">
                            <div class="timeline-dot"
                                style="background: var(--text-muted); box-shadow: 0 0 0 3px rgba(156,163,175,0.2);"></div>
                            <div class="timeline-content">
                                <div class="timeline-label">Last Updated</div>
                                <div class="timeline-desc">
                                    {{ $activity->updated_at?->format('d M Y, H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Accept Modal --}}
    @if (auth()->user()->is_dosen && $activity->is_pending)
        <div class="modal fade" id="acceptModal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('activities.accept', $activity) }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title fw-bold">
                                <i class="bi bi-check-circle-fill me-2" style="color: var(--success);"></i> Accept
                                Activity
                            </h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p style="color: var(--text-muted); font-size: 0.85rem;" class="mb-3">
                                You are about to accept <strong
                                    style="color: var(--text-primary);">"{{ $activity->title }}"</strong>.
                                You may add an optional note for the user.
                            </p>
                            <div class="mb-0">
                                <label class="form-label">Dosen Note <span
                                        style="color: var(--text-muted); font-size: 0.8rem;">(optional)</span></label>
                                <textarea name="dosen_note" class="form-control" rows="3"
                                    placeholder="Add a note or feedback for the user..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn"
                                style="background: var(--success); color: #fff; font-weight: 600;">
                                <i class="bi bi-check-lg me-1"></i> Accept Activity
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Reject Modal --}}
    @if (auth()->user()->is_dosen && $activity->is_pending)
        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('activities.reject', $activity) }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title fw-bold">
                                <i class="bi bi-x-circle-fill me-2" style="color: var(--danger);"></i> Reject Activity
                            </h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p style="color: var(--text-muted); font-size: 0.85rem;" class="mb-3">
                                You are about to reject <strong
                                    style="color: var(--text-primary);">"{{ $activity->title }}"</strong>.
                                Please provide a reason.
                            </p>
                            <div class="mb-3">
                                <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                <textarea name="reject_reason" class="form-control" rows="4"
                                    placeholder="Explain why this activity is being rejected..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn"
                                style="background: var(--danger); color: #fff; font-weight: 600;">
                                <i class="bi bi-x-lg me-1"></i> Reject Activity
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

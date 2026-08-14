@extends('layouts.app')

@section('title', $activity->title)

@section('styles')
    <style>
        /* Show page specific styles */
        .show-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .show-header .activity-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 0.5rem;
        }

        .show-header .meta-item {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.85rem;
            color: #6c757d;
            padding: 0.25rem 0.65rem;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 2rem;
        }

        .detail-section-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6c757d;
            margin-bottom: 0.75rem;
        }

        .detail-content {
            font-size: 0.925rem;
            line-height: 1.7;
            color: #495057;
            white-space: pre-line;
        }

        .info-list .info-item {
            display: flex;
            align-items: flex-start;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-list .info-item:last-child {
            border-bottom: none;
        }

        .info-list .info-icon {
            width: 32px;
            height: 32px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-right: 0.75rem;
            font-size: 0.85rem;
        }

        .info-list .info-label {
            font-size: 0.75rem;
            color: #adb5bd;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 0.1rem;
        }

        .info-list .info-value {
            font-size: 0.875rem;
            color: #212529;
            font-weight: 500;
        }

        .attachment-grid .attachment-card {
            border: none;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }

        .attachment-grid .attachment-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.12);
        }

        .attachment-grid .attachment-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }

        .attachment-grid .attachment-caption {
            padding: 0.6rem 0.85rem;
            font-size: 0.8rem;
            color: #6c757d;
            background: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Image lightbox modal */
        .lightbox-modal .modal-content {
            background: rgba(0, 0, 0, 0.92);
            border: none;
        }

        .lightbox-modal .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.75rem 1rem;
        }

        .lightbox-modal .modal-title {
            color: #fff;
            font-size: 0.9rem;
        }

        .lightbox-modal .btn-close {
            filter: invert(1);
        }

        .lightbox-modal .modal-body {
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 50vh;
        }

        .lightbox-modal .modal-body img {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
        }

        .status-timeline {
            position: relative;
            padding-left: 1.75rem;
        }

        .status-timeline::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .status-timeline .timeline-item {
            position: relative;
            padding-bottom: 1rem;
        }

        .status-timeline .timeline-item:last-child {
            padding-bottom: 0;
        }

        .status-timeline .timeline-dot {
            position: absolute;
            left: -1.5rem;
            top: 0.15rem;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
        }

        .status-timeline .timeline-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #495057;
        }

        .status-timeline .timeline-desc {
            font-size: 0.78rem;
            color: #adb5bd;
        }
    </style>
@endsection

@section('content')
    {{-- Back Button + Actions Bar --}}
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>

        <div class="ms-auto d-flex flex-wrap gap-2">
            {{-- Edit button --}}
            @if (
                $activity->status !== 'accept' &&
                    (auth()->user()->role === 'admin' ||
                        $activity->user_id === auth()->id() ||
                        $activity->company_id === auth()->user()->company_id))
                <a href="{{ route('activities.edit', $activity) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
            @endif

            {{-- Delete (admin only) --}}
            @if (auth()->user()->role === 'admin')
                <form method="POST" action="{{ route('activities.destroy', $activity) }}"
                    onsubmit="return confirm('Delete this activity?')" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </form>
            @endif

            {{-- Dosen: Accept / Reject --}}
            @if (auth()->user()->role === 'dosen' && $activity->status === 'pending')
                <form method="POST" action="{{ route('activities.accept', $activity) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-check-lg me-1"></i> Accept
                    </button>
                </form>
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-lg me-1"></i> Reject
                </button>
            @endif
        </div>
    </div>

    {{-- Header Card --}}
    <div class="show-header">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
            <h2 class="activity-title mb-0">{{ $activity->title }}</h2>
            @if ($activity->status === 'accept')
                <span class="badge bg-success fs-6 px-3 py-2 rounded-pill">
                    <i class="bi bi-check-circle me-1"></i> Accepted
                </span>
            @elseif ($activity->status === 'reject')
                <span class="badge bg-danger fs-6 px-3 py-2 rounded-pill">
                    <i class="bi bi-x-circle me-1"></i> Rejected
                </span>
            @else
                <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill">
                    <i class="bi bi-hourglass-split me-1"></i> Pending
                </span>
            @endif
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="meta-item">
                <i class="bi bi-building"></i> {{ $activity->company->name ?? '-' }}
            </span>
            <span class="meta-item">
                <i class="bi bi-person"></i> {{ $activity->user->name ?? '-' }}
            </span>
            <span class="meta-item">
                <i class="bi bi-calendar3"></i> {{ $activity->date?->format('d M Y, H:i') ?? '-' }}
            </span>
            <span class="meta-item">
                <i class="bi bi-hash"></i> #{{ $activity->id }}
            </span>
        </div>
    </div>

    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Description --}}
            @if ($activity->descriptions)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-text-left me-2 text-primary"></i> Description
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="detail-content">{{ $activity->descriptions }}</div>
                    </div>
                </div>
            @endif

            {{-- Rules --}}
            @if ($activity->rules)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-list-check me-2 text-warning"></i> Rules
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="detail-content">{{ $activity->rules }}</div>
                    </div>
                </div>
            @endif

            {{-- Tools --}}
            @if ($activity->tools)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-tools me-2 text-info"></i> Tools
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="detail-content">{{ $activity->tools }}</div>
                    </div>
                </div>
            @endif

            {{-- Attachments --}}
            @if ($activity->attachments->count())
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-images me-2 text-success"></i> Attachments
                        </h6>
                        <span class="badge bg-light text-muted">{{ $activity->attachments->count() }}
                            {{ Str::plural('image', $activity->attachments->count()) }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 attachment-grid">
                            @foreach ($activity->attachments as $att)
                                <div class="col-sm-6 col-md-4">
                                    <div class="attachment-card" data-bs-toggle="modal"
                                        data-bs-target="#lightbox{{ $att->id }}">
                                        <img src="{{ Storage::url($att->image_url) }}"
                                            alt="{{ $att->caption ?: 'Attachment' }}">
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
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">
                                    <i class="bi bi-image me-2"></i>{{ $att->caption ?: 'Attachment' }}
                                </h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
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
            @if ($activity->status === 'reject' && $activity->reject_reason)
                <div class="card border-0 shadow-sm mb-4 border-start border-danger border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-2">
                            <div class="rounded-circle bg-danger bg-opacity-10 p-2 flex-shrink-0">
                                <i class="bi bi-exclamation-triangle text-danger"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-danger mb-1">Rejection Reason</h6>
                                <p class="mb-2" style="font-size: 0.9rem;">{{ $activity->reject_reason }}</p>
                                @if ($activity->rejector)
                                    <small class="text-muted">
                                        <i class="bi bi-person me-1"></i> {{ $activity->rejector->name }}
                                        &middot; {{ $activity->reject_at?->format('d M Y, H:i') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Acceptance Info --}}
            @if ($activity->status === 'accept')
                <div class="card border-0 shadow-sm mb-4 border-start border-success border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-2">
                            <div class="rounded-circle bg-success bg-opacity-10 p-2 flex-shrink-0">
                                <i class="bi bi-check-circle text-success"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-success mb-1">Activity Accepted</h6>
                                @if ($activity->acceptor)
                                    <small class="text-muted">
                                        <i class="bi bi-person me-1"></i> {{ $activity->acceptor->name }}
                                        &middot; {{ $activity->accept_at?->format('d M Y, H:i') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Activity Info Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-info-circle me-2 text-primary"></i> Activity Details
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="info-list px-3">
                        <div class="info-item">
                            <div class="info-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-hash"></i>
                            </div>
                            <div>
                                <div class="info-label">Activity ID</div>
                                <div class="info-value">#{{ $activity->id }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon bg-info bg-opacity-10 text-info">
                                <i class="bi bi-building"></i>
                            </div>
                            <div>
                                <div class="info-label">Company</div>
                                <div class="info-value">{{ $activity->company->name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <div class="info-label">Created By</div>
                                <div class="info-value">{{ $activity->user->name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-calendar3"></i>
                            </div>
                            <div>
                                <div class="info-label">Date</div>
                                <div class="info-value">{{ $activity->date?->format('d M Y, H:i') ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Timeline Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-clock-history me-2 text-secondary"></i> Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="status-timeline">
                        <div class="timeline-item">
                            <div class="timeline-dot bg-primary"></div>
                            <div class="timeline-label">Created</div>
                            <div class="timeline-desc">
                                {{ $activity->created_at?->format('d M Y, H:i') }}
                            </div>
                        </div>

                        @if ($activity->re_submit_at)
                            <div class="timeline-item">
                                <div class="timeline-dot bg-info"></div>
                                <div class="timeline-label">Resubmitted</div>
                                <div class="timeline-desc">
                                    {{ $activity->re_submit_at?->format('d M Y, H:i') }}
                                </div>
                            </div>
                        @endif

                        @if ($activity->status === 'accept')
                            <div class="timeline-item">
                                <div class="timeline-dot bg-success"></div>
                                <div class="timeline-label">Accepted</div>
                                <div class="timeline-desc">
                                    {{ $activity->accept_at?->format('d M Y, H:i') }}
                                    @if ($activity->acceptor)
                                        &middot; {{ $activity->acceptor->name }}
                                    @endif
                                </div>
                            </div>
                        @elseif ($activity->status === 'reject')
                            <div class="timeline-item">
                                <div class="timeline-dot bg-danger"></div>
                                <div class="timeline-label">Rejected</div>
                                <div class="timeline-desc">
                                    {{ $activity->reject_at?->format('d M Y, H:i') }}
                                    @if ($activity->rejector)
                                        &middot; {{ $activity->rejector->name }}
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="timeline-item">
                                <div class="timeline-dot bg-warning"></div>
                                <div class="timeline-label">Pending Review</div>
                                <div class="timeline-desc">Awaiting approval</div>
                            </div>
                        @endif

                        <div class="timeline-item">
                            <div class="timeline-dot bg-secondary"></div>
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

    {{-- Reject Modal --}}
    @if (auth()->user()->role === 'dosen' && $activity->status === 'pending')
        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('activities.reject', $activity) }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-x-circle me-2 text-danger"></i> Reject Activity
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted small mb-3">
                                You are about to reject <strong>"{{ $activity->title }}"</strong>.
                                Please provide a reason.
                            </p>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Rejection Reason <span
                                        class="text-danger">*</span></label>
                                <textarea name="reject_reason" class="form-control" rows="4"
                                    placeholder="Explain why this activity is being rejected..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-lg me-1"></i> Reject Activity
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

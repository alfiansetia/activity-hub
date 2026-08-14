@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    @include('partials.breadcrumb', [
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'bi bi-house-door'],
            ['label' => 'My Profile'],
        ],
    ])

    {{-- Company Join Request (no company assigned) --}}
    @if (!$user->company_id && $user->is_user)
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
    @if ($user->company_id && $user->company_status !== 'accept')
        <div class="fade-up mb-4">
            @if ($user->company_status === 'reject')
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
                        @if ($user->company_reject_reason)
                            <div class="mt-2 p-2 rounded"
                                style="background: rgba(239,68,68,0.06); font-size: 0.85rem; color: var(--text-primary);">
                                <strong>Reason:</strong> {{ $user->company_reject_reason }}
                            </div>
                        @endif
                        @if ($user->company_reject_at)
                            <small style="color: var(--text-muted);">
                                Rejected on {{ $user->company_reject_at->format('d M Y, H:i') }}
                            </small>
                        @endif
                        <form method="POST" action="{{ route('profile.company-request') }}"
                            class="mt-3 d-flex gap-2 align-items-end">
                            @csrf
                            <select name="company_id" class="form-select form-select-sm" style="max-width: 250px;" required>
                                <option value="">-- Select Company --</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}"
                                        {{ $company->id == $user->company_id ? 'selected' : '' }}>
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

    <div class="row g-4">
        {{-- Profile Card --}}
        <div class="col-lg-4 fade-up fade-up-delay-1">
            <div class="card">
                <div class="card-body">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                        <span
                            class="badge {{ $user->is_admin ? 'badge-soft-primary' : ($user->is_dosen ? 'badge-soft-info' : 'bg-secondary') }} px-3 py-1 rounded-pill">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="profile-info-row">
                        <span class="profile-info-label">Email</span>
                        <span class="profile-info-value">{{ $user->email }}</span>
                    </div>
                    <div class="profile-info-row">
                        <span class="profile-info-label">Company</span>
                        <span class="profile-info-value">{{ $user->company->name ?? 'None' }}</span>
                    </div>
                    <div class="profile-info-row">
                        <span class="profile-info-label">Status</span>
                        <span>
                            @if ($user->company_status === 'accept')
                                <span class="badge badge-soft-success"><i
                                        class="bi bi-check-circle me-1"></i>Approved</span>
                            @elseif ($user->company_status === 'reject')
                                <span class="badge badge-soft-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                            @else
                                <span class="badge badge-soft-warning"><i
                                        class="bi bi-hourglass-split me-1"></i>Pending</span>
                            @endif
                        </span>
                    </div>

                    {{-- Approved Details --}}
                    @if ($user->company_status === 'accept' && $user->company)
                        @if ($user->company_accept_at)
                            <div class="profile-info-row">
                                <span class="profile-info-label">Approved At</span>
                                <span
                                    class="profile-info-value">{{ $user->company_accept_at->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                        @if ($user->companyAcceptBy)
                            <div class="profile-info-row">
                                <span class="profile-info-label">Approved By</span>
                                <span class="profile-info-value">{{ $user->companyAcceptBy->name }}</span>
                            </div>
                        @endif
                    @endif

                    {{-- Rejected Details --}}
                    @if ($user->company_status === 'reject' && $user->company)
                        @if ($user->company_reject_reason)
                            <div class="profile-info-row">
                                <span class="profile-info-label">Reject Reason</span>
                                <span class="profile-info-value"
                                    style="color: var(--danger);">{{ $user->company_reject_reason }}</span>
                            </div>
                        @endif
                        @if ($user->company_reject_at)
                            <div class="profile-info-row">
                                <span class="profile-info-label">Rejected At</span>
                                <span
                                    class="profile-info-value">{{ $user->company_reject_at->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                        @if ($user->companyRejectBy)
                            <div class="profile-info-row">
                                <span class="profile-info-label">Rejected By</span>
                                <span class="profile-info-value">{{ $user->companyRejectBy->name }}</span>
                            </div>
                        @endif
                    @endif

                    <div class="profile-info-row">
                        <span class="profile-info-label">Joined</span>
                        <span class="profile-info-value">{{ $user->created_at?->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Forms --}}
        <div class="col-lg-8">
            {{-- Edit Name --}}
            <div class="card mb-4 fade-up fade-up-delay-2">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-person-fill me-2" style="color: var(--primary);"></i> Edit Profile
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" readonly disabled>
                            <small style="color: var(--text-muted); font-size: 0.75rem;">Email cannot be
                                changed.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="card fade-up fade-up-delay-3">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-shield-lock-fill me-2" style="color: var(--warning);"></i> Change Password
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" id="current_password" name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" id="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-warning text-dark">
                            <i class="bi bi-key-fill me-1"></i> Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

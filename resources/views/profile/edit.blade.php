@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <div class="d-flex align-items-center gap-2 mb-4 fade-up">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
        <h4 class="fw-bold mb-0">My Profile</h4>
    </div>

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
                            class="badge {{ $user->role === 'admin' ? 'badge-soft-primary' : ($user->role === 'dosen' ? 'badge-soft-info' : 'bg-secondary') }} px-3 py-1 rounded-pill">
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
                                <span class="badge badge-soft-success">Approved</span>
                            @elseif ($user->company_status === 'reject')
                                <span class="badge badge-soft-danger">Rejected</span>
                            @else
                                <span class="badge badge-soft-warning">Pending</span>
                            @endif
                        </span>
                    </div>
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
                            <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
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

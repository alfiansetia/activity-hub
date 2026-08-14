@extends('layouts.app')

@section('title', 'My Profile')

@section('styles')
    <style>
        .profile-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 0.75rem;
            padding: 2rem;
            text-align: center;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            margin: 0 auto 1rem;
            box-shadow: 0 0.25rem 0.5rem rgba(13, 110, 253, 0.3);
        }

        .profile-info-row {
            display: flex;
            align-items: center;
            padding: 0.65rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .profile-info-row:last-child {
            border-bottom: none;
        }

        .profile-info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #adb5bd;
            width: 100px;
            flex-shrink: 0;
        }

        .profile-info-value {
            font-size: 0.9rem;
            font-weight: 500;
            color: #212529;
        }
    </style>
@endsection

@section('content')
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
        <h4 class="fw-semibold mb-0">My Profile</h4>
    </div>

    <div class="row g-4">
        {{-- Profile Card --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                        <span
                            class="badge {{ $user->role === 'admin' ? 'bg-primary' : ($user->role === 'dosen' ? 'bg-info text-dark' : 'bg-secondary') }} px-3 py-1 rounded-pill">
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
                                <span class="badge bg-success">Approved</span>
                            @elseif ($user->company_status === 'reject')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
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
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-person me-2 text-primary"></i> Edit Profile
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Full Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="name" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" value="{{ $user->email }}" readonly disabled>
                            <small class="text-muted">Email cannot be changed.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-shield-lock me-2 text-warning"></i> Change Password
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold">Current Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" id="current_password" name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">New Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" id="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-warning text-dark">
                            <i class="bi bi-key me-1"></i> Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

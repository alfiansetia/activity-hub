@extends('layouts.guest')

@section('title', 'Register')

@section('content')
    <div class="auth-card fade-up">
        <h2 class="auth-card-title">Create your account</h2>
        <p class="auth-card-subtitle">Join Activity Hub to start managing activities</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Name --}}
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                    value="{{ old('name') }}" placeholder="John Doe" required autofocus>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                    name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Company --}}
            <div class="mb-3">
                <label for="company_id" class="form-label">
                    Company <span style="color: var(--text-muted); font-weight: 400;">(optional)</span>
                </label>
                <select class="form-select @error('company_id') is-invalid @enderror" id="company_id" name="company_id">
                    <option value="">— Select Company —</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
                @error('company_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="d-block mt-1" style="color: var(--text-muted); font-size: 0.75rem;">
                    <i class="bi bi-info-circle me-1"></i>Your account will need admin approval before access is granted.
                </small>
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group-password">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" placeholder="••••••••" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Confirm Password --}}
            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="input-group-password">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                        placeholder="••••••••" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn btn-primary w-100 py-2">
                <i class="bi bi-person-plus me-1"></i> Create Account
            </button>
        </form>
    </div>

    <p class="auth-footer fade-up fade-up-delay-1">
        Already have an account?
        <a href="{{ route('login') }}">Sign in</a>
    </p>
@endsection

@push('scripts')
    <script>
        function togglePassword(fieldId, btn) {
            const field = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                field.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    </script>
@endpush

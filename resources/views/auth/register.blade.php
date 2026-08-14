@extends('layouts.guest')

@section('title', 'Register')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h5 class="fw-semibold text-center mb-4">Create your account</h5>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Name --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Company --}}
                <div class="mb-3">
                    <label for="company_id" class="form-label">
                        Company <span class="text-muted">(optional)</span>
                    </label>
                    <select class="form-select @error('company_id') is-invalid @enderror" id="company_id" name="company_id">
                        <option value="">-- Select Company --</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Your account will need admin approval before access is granted.</small>
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                        required>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-person-plus me-1"></i> Register
                </button>
            </form>
        </div>
    </div>

    <p class="text-center text-muted small mt-3">
        Already have an account?
        <a href="{{ route('login') }}">Log in</a>
    </p>
@endsection

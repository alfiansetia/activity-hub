@extends('layouts.app')

@section('title', 'Add New User')

@section('content')
    @include('partials.breadcrumb', [
        'breadcrumbs' => [
            ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'bi bi-house-door'],
            ['label' => 'User Management', 'url' => route('admin.users.index')],
            ['label' => 'Add New User'],
        ],
    ])

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="bi bi-person-plus me-2"></i>Add New User
                    </h6>
                </div>
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="card-body">
                        {{-- Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                placeholder="Enter full name" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                placeholder="Enter email address" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            {{-- Password --}}
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-semibold">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Minimum 8 characters" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label fw-semibold">
                                    Confirm Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" placeholder="Re-enter password" required>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Role --}}
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label fw-semibold">
                                    Role <span class="text-danger">*</span>
                                </label>
                                <select name="role" id="role"
                                    class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                                    <option value="dosen" {{ old('role') === 'dosen' ? 'selected' : '' }}>Dosen</option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Company --}}
                            <div class="col-md-6 mb-3">
                                <label for="company_id" class="form-label fw-semibold">Company</label>
                                <select name="company_id" id="company_id"
                                    class="form-select @error('company_id') is-invalid @enderror">
                                    <option value="">No Company</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}"
                                            {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Company Status --}}
                        <div class="mb-3">
                            <label for="company_status" class="form-label fw-semibold">
                                Company Status <span class="text-danger">*</span>
                            </label>
                            <select name="company_status" id="company_status"
                                class="form-select @error('company_status') is-invalid @enderror" required>
                                <option value="none" {{ old('company_status', 'none') === 'none' ? 'selected' : '' }}>
                                    None</option>
                                <option value="pending" {{ old('company_status') === 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="accept" {{ old('company_status') === 'accept' ? 'selected' : '' }}>Accepted
                                </option>
                                <option value="reject" {{ old('company_status') === 'reject' ? 'selected' : '' }}>Rejected
                                </option>
                            </select>
                            @error('company_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus me-1"></i> Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

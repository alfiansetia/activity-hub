@extends('errors.layout')

@section('title', '403 — Forbidden')
@section('description', 'Access forbidden — Activity Hub')

@section('content')
    <div class="error-code fade-up">403</div>

    <div class="error-badge fade-up fade-up-delay-1">
        <i class="bi bi-shield-lock"></i>
        Access Forbidden
    </div>

    <h1 class="error-title fade-up fade-up-delay-2">
        Access <span class="gradient-text">Denied</span>
    </h1>

    <p class="error-subtitle fade-up fade-up-delay-3">
        You don't have permission to access this page.
        Please contact your administrator if you believe this is an error.
    </p>

    <div class="error-actions fade-up fade-up-delay-4">
        <a href="{{ url('/dashboard') }}" class="hero-btn-primary">
            <i class="bi bi-grid-1x2-fill"></i> Go to Dashboard
        </a>
        <a href="{{ url('/') }}" class="hero-btn-secondary">
            <i class="bi bi-house"></i> Back to Home
        </a>
    </div>
@endsection

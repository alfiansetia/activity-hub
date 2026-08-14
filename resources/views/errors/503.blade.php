@extends('errors.layout')

@section('title', '503 — Service Unavailable')
@section('description', 'Service unavailable — Activity Hub')

@section('content')
    <div class="error-code fade-up">503</div>

    <div class="error-badge fade-up fade-up-delay-1">
        <i class="bi bi-tools"></i>
        Under Maintenance
    </div>

    <h1 class="error-title fade-up fade-up-delay-2">
        We'll Be <span class="gradient-text">Right Back</span>
    </h1>

    <p class="error-subtitle fade-up fade-up-delay-3">
        We're currently performing scheduled maintenance.
        Please check back in a few minutes.
    </p>

    <div class="error-actions fade-up fade-up-delay-4">
        <a href="javascript:location.reload()" class="hero-btn-primary">
            <i class="bi bi-arrow-clockwise"></i> Check Again
        </a>
        <a href="{{ url('/') }}" class="hero-btn-secondary">
            <i class="bi bi-house"></i> Back to Home
        </a>
    </div>
@endsection

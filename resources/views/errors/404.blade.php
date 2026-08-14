@extends('errors.layout')

@section('title', '404 — Page Not Found')
@section('description', 'Page not found — Activity Hub')

@section('content')
    <div class="error-code fade-up">404</div>

    <div class="error-badge fade-up fade-up-delay-1">
        <i class="bi bi-search"></i>
        Page Not Found
    </div>

    <h1 class="error-title fade-up fade-up-delay-2">
        Lost in <span class="gradient-text">Space</span>
    </h1>

    <p class="error-subtitle fade-up fade-up-delay-3">
        The page you're looking for doesn't exist or has been moved.
        Let's get you back on track.
    </p>

    <div class="error-actions fade-up fade-up-delay-4">
        <a href="{{ url('/') }}" class="hero-btn-primary">
            <i class="bi bi-house"></i> Back to Home
        </a>
        <a href="javascript:history.back()" class="hero-btn-secondary">
            <i class="bi bi-arrow-left"></i> Go Back
        </a>
    </div>
@endsection

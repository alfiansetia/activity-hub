@extends('errors.layout')

@section('title', '500 — Server Error')
@section('description', 'Server error — Activity Hub')

@section('content')
    <div class="error-code fade-up">500</div>

    <div class="error-badge fade-up fade-up-delay-1">
        <i class="bi bi-exclamation-triangle"></i>
        Internal Server Error
    </div>

    <h1 class="error-title fade-up fade-up-delay-2">
        Something Went <span class="gradient-text">Wrong</span>
    </h1>

    <p class="error-subtitle fade-up fade-up-delay-3">
        We're experiencing some technical difficulties.
        Our team has been notified and is working on a fix.
    </p>

    <div class="error-actions fade-up fade-up-delay-4">
        <a href="javascript:location.reload()" class="hero-btn-primary">
            <i class="bi bi-arrow-clockwise"></i> Try Again
        </a>
        <a href="{{ url('/') }}" class="hero-btn-secondary">
            <i class="bi bi-house"></i> Back to Home
        </a>
    </div>
@endsection

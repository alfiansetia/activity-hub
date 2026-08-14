@extends('errors.layout')

@section('title', '429 — Too Many Requests')
@section('description', 'Too many requests — Activity Hub')

@section('content')
    <div class="error-code fade-up">429</div>

    <div class="error-badge fade-up fade-up-delay-1">
        <i class="bi bi-speedometer"></i>
        Too Many Requests
    </div>

    <h1 class="error-title fade-up fade-up-delay-2">
        Slow <span class="gradient-text">Down</span>
    </h1>

    <p class="error-subtitle fade-up fade-up-delay-3">
        You've made too many requests in a short period.
        Please wait a moment and try again.
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

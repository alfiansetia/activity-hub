@extends('errors.layout')

@section('title', '419 — Page Expired')
@section('description', 'Page expired — Activity Hub')

@section('content')
    <div class="error-code fade-up">419</div>

    <div class="error-badge fade-up fade-up-delay-1">
        <i class="bi bi-clock-history"></i>
        Page Expired
    </div>

    <h1 class="error-title fade-up fade-up-delay-2">
        Session <span class="gradient-text">Expired</span>
    </h1>

    <p class="error-subtitle fade-up fade-up-delay-3">
        Your session has expired due to inactivity.
        Please refresh the page and try again.
    </p>

    <div class="error-actions fade-up fade-up-delay-4">
        <a href="javascript:location.reload()" class="hero-btn-primary">
            <i class="bi bi-arrow-clockwise"></i> Refresh Page
        </a>
        <a href="{{ url('/') }}" class="hero-btn-secondary">
            <i class="bi bi-house"></i> Back to Home
        </a>
    </div>
@endsection

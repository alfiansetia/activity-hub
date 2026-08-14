<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="Activity Hub — A centralized platform for creating, reviewing, and tracking activities across your organization.">

    <title>{{ config('app.name', 'Activity Hub') }} — Manage Activities Effortlessly</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body>

    {{-- Navbar --}}
    <nav class="landing-navbar" id="landingNavbar">
        <a href="{{ url('/') }}" class="landing-brand">
            <div class="landing-brand-icon">
                <i class="bi bi-activity"></i>
            </div>
            <span class="landing-brand-text">Activity Hub</span>
        </a>

        <button class="landing-toggler" onclick="document.getElementById('navLinks').classList.toggle('show')">
            <i class="bi bi-list"></i>
        </button>

        <ul class="landing-nav-links" id="navLinks">
            @auth
                <li>
                    <a class="landing-nav-btn" href="{{ url('/dashboard') }}">
                        <i class="bi bi-grid-1x2-fill"></i> Dashboard
                    </a>
                </li>
            @else
                <li>
                    <a class="landing-nav-link" href="{{ route('login') }}">Sign in</a>
                </li>
                @if (Route::has('register'))
                    <li>
                        <a class="landing-nav-btn" href="{{ route('register') }}">
                            Get Started <i class="bi bi-arrow-right"></i>
                        </a>
                    </li>
                @endif
            @endauth
        </ul>
    </nav>

    {{-- Hero --}}
    <section class="hero-section">
        <div class="hero-shapes">
            <div class="hero-shape"></div>
            <div class="hero-shape"></div>
            <div class="hero-shape"></div>
        </div>

        <div class="container position-relative" style="z-index: 2;">
            <div class="row justify-content-center">
                <div class="col-lg-9 text-center">
                    <div class="hero-badge fade-up">
                        <i class="bi bi-stars"></i>
                        Streamline Your Activity Management
                    </div>

                    <h1 class="hero-title fade-up fade-up-delay-1">
                        Manage Activities<br>
                        <span class="gradient-text">Effortlessly</span>
                    </h1>

                    <p class="hero-subtitle fade-up fade-up-delay-2">
                        A centralized platform for creating, reviewing, and tracking activities
                        across your organization. Streamline workflows between Users, Lecturers, and Admins.
                    </p>

                    <div class="hero-actions fade-up fade-up-delay-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="hero-btn-primary">
                                <i class="bi bi-grid-1x2-fill"></i> Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="hero-btn-primary">
                                <i class="bi bi-rocket-takeoff"></i> Get Started Free
                            </a>
                            <a href="{{ route('login') }}" class="hero-btn-secondary">
                                <i class="bi bi-box-arrow-in-right"></i> Sign in
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="features-section">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-label">
                    <i class="bi bi-lightning-charge-fill"></i> Features
                </div>
                <h2 class="section-title">How It Works</h2>
                <p class="section-subtitle mx-auto">Simple workflow for everyone in your organization</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card fade-up">
                        <div class="feature-icon" style="background: rgba(99,102,241,0.1); color: var(--primary);">
                            <i class="bi bi-plus-circle-fill"></i>
                        </div>
                        <h5>Create Activities</h5>
                        <p>Users create activities with title, description, rules, and tools. Attach cropped images
                            for visual reference.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card fade-up fade-up-delay-1">
                        <div class="feature-icon" style="background: var(--success-light); color: var(--success);">
                            <i class="bi bi-check2-square"></i>
                        </div>
                        <h5>Review & Approve</h5>
                        <p>Lecturers review pending activities with Accept or Reject decisions, providing clear
                            feedback.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card fade-up fade-up-delay-2">
                        <div class="feature-icon" style="background: var(--warning-light); color: var(--warning);">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <h5>Resubmit & Track</h5>
                        <p>Rejected activities can be edited and resubmitted. Track full activity status from a
                            unified dashboard.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="stats-section">
        <div class="container">
            <div class="row g-0">
                <div class="col-6 col-md-3">
                    <div class="stat-item fade-up">
                        <div class="stat-number">3</div>
                        <div class="stat-text">User Roles</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item fade-up fade-up-delay-1">
                        <div class="stat-number">&infin;</div>
                        <div class="stat-text">Activities</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item fade-up fade-up-delay-2">
                        <div class="stat-number">3</div>
                        <div class="stat-text">Status Levels</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-item fade-up fade-up-delay-3">
                        <div class="stat-number"><i class="bi bi-crop"></i></div>
                        <div class="stat-text">Image Cropper</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="cta-section">
        <div class="container">
            <div class="cta-card fade-up">
                <h2 class="cta-title">Ready to get started?</h2>
                <p class="cta-text">
                    Join Activity Hub today and streamline your organization's activity management workflow.
                </p>
                <div class="hero-actions" style="position: relative;">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="hero-btn-primary">
                            <i class="bi bi-grid-1x2-fill"></i> Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="hero-btn-primary">
                            <i class="bi bi-rocket-takeoff"></i> Create Free Account
                        </a>
                        <a href="{{ route('login') }}" class="hero-btn-secondary">
                            <i class="bi bi-box-arrow-in-right"></i> Sign in
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="landing-footer">
        <div class="container">
            <small>&copy; {{ date('Y') }} Activity Hub. Built with
                <a href="https://laravel.com" target="_blank">Laravel</a> &
                <a href="https://getbootstrap.com" target="_blank">Bootstrap 5</a>.
            </small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll effect
        const navbar = document.getElementById('landingNavbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
</body>

</html>

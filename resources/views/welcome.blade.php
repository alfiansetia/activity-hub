<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Activity Hub') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
                <i class="bi bi-activity text-primary me-2 fs-4"></i>
                Activity Hub
            </a>
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#landingNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="landingNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                    @auth
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm" href="{{ url('/dashboard') }}">
                                <i class="bi bi-grid-1x2 me-1"></i> Dashboard
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Log in</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="btn btn-primary btn-sm ms-lg-2" href="{{ route('register') }}">Register</a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">Manage Your Activities<br>Effortlessly</h1>
                    <p class="lead mb-4 opacity-90">
                        A centralized platform for creating, reviewing, and tracking activities across your
                        organization.
                        Streamline workflows between Users, Lecturers, and Admins.
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-light btn-lg px-4">
                                <i class="bi bi-grid-1x2 me-2"></i> Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-light btn-lg px-4">
                                <i class="bi bi-person-plus me-2"></i> Get Started
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Log in
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">How It Works</h2>
                <p class="text-muted">Simple workflow for everyone in your organization</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card h-100 p-4">
                        <div class="card-body text-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 56px; height: 56px;">
                                <i class="bi bi-plus-circle fs-4"></i>
                            </div>
                            <h5 class="fw-semibold">Create Activities</h5>
                            <p class="text-muted small mb-0">
                                Users create activities with title, description, rules, and tools. Attach cropped images
                                for visual reference.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card h-100 p-4">
                        <div class="card-body text-center">
                            <div class="bg-success bg-opacity-10 text-success rounded-3 d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 56px; height: 56px;">
                                <i class="bi bi-check2-square fs-4"></i>
                            </div>
                            <h5 class="fw-semibold">Review &amp; Approve</h5>
                            <p class="text-muted small mb-0">
                                Lecturers review pending activities with Accept or Reject decisions, providing clear
                                feedback.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card h-100 p-4">
                        <div class="card-body text-center">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 56px; height: 56px;">
                                <i class="bi bi-arrow-repeat fs-4"></i>
                            </div>
                            <h5 class="fw-semibold">Resubmit &amp; Track</h5>
                            <p class="text-muted small mb-0">
                                Rejected activities can be edited and resubmitted. Track full activity status from a
                                unified dashboard.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="bg-white py-5 border-top">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-6 col-md-3">
                    <div class="display-6 fw-bold text-primary">3</div>
                    <p class="text-muted small mb-0">User Roles</p>
                </div>
                <div class="col-6 col-md-3">
                    <div class="display-6 fw-bold text-primary">&infin;</div>
                    <p class="text-muted small mb-0">Activities</p>
                </div>
                <div class="col-6 col-md-3">
                    <div class="display-6 fw-bold text-primary">3</div>
                    <p class="text-muted small mb-0">Status Levels</p>
                </div>
                <div class="col-6 col-md-3">
                    <div class="display-6 fw-bold text-primary"><i class="bi bi-crop"></i></div>
                    <p class="text-muted small mb-0">Image Cropper</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="py-4 bg-dark text-center">
        <div class="container">
            <small class="text-secondary">&copy; {{ date('Y') }} Activity Hub. Built with Laravel &amp; Bootstrap
                5.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

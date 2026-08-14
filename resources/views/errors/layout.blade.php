<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', 'An error occurred — Activity Hub')">

    <title>@yield('title', 'Error') | {{ config('app.name', 'Activity Hub') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body>

    {{-- Navbar --}}
    <nav class="landing-navbar scrolled">
        <a href="{{ url('/') }}" class="landing-brand">
            <div class="landing-brand-icon">
                <i class="bi bi-activity"></i>
            </div>
            <span class="landing-brand-text">Activity Hub</span>
        </a>

        <ul class="landing-nav-links">
            <li>
                <a class="landing-nav-link" href="{{ url('/') }}">
                    <i class="bi bi-house"></i> Home
                </a>
            </li>
            <li>
                <a class="landing-nav-btn" href="{{ url('/dashboard') }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
            </li>
        </ul>
    </nav>

    {{-- Error Hero --}}
    <section class="error-hero-section">
        <div class="hero-shapes">
            <div class="hero-shape"></div>
            <div class="hero-shape"></div>
            <div class="hero-shape"></div>
        </div>

        <div class="container position-relative" style="z-index: 2;">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    @yield('content')
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
</body>

</html>

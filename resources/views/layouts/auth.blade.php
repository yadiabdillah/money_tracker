<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Authentication') - {{ config('app.name', 'Money Tracker') }}</title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">

                <!-- Logo & Brand Header -->
                <div class="text-center mb-4">
                    <a href="{{ url('/') }}" class="text-decoration-none">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-2" style="width: 64px; height: 64px;">
                            <i class="bi bi-wallet2" style="font-size: 2rem;"></i>
                        </div>
                        <h1 class="h4 fw-bold text-dark mb-0">{{ config('app.name', 'Money Tracker') }}</h1>
                    </a>
                    <p class="text-muted small mt-1">Smart, Shared Family Financial Management</p>
                </div>

                <!-- Global Alert Notifications -->
                @if (session('status'))
                    <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="bi bi-info-circle me-2"></i> {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Auth Card Container -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 p-sm-5">
                        @yield('content')
                    </div>
                </div>

                <!-- Footer Copyright -->
                <div class="text-center mt-4">
                    <p class="text-muted small mb-0">&copy; {{ date('Y') }} Money Tracker. Local-First Family System.</p>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts Stack -->
    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - {{ config('app.name', 'Money Tracker') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">

    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                <i class="bi bi-wallet2"></i> {{ config('app.name', 'Money Tracker') }}
            </a>

            <div class="d-flex align-items-center gap-3">
                <div class="text-white small text-end d-none d-sm-block">
                    <div class="fw-semibold">{{ $user->name }}</div>
                    <div class="opacity-75">{{ $user->family->name ?? 'No Family' }} ({{ ucfirst($user->role) }})</div>
                </div>

                <!-- Logout Form -->
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <button type="submit" id="logout-btn" class="btn btn-sm btn-outline-light d-flex align-items-center gap-1">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container py-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <!-- Account & Family Information Card -->
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                                <i class="bi bi-person-badge fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">{{ $user->name }}</h5>
                                <span class="badge {{ $user->role === 'admin' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    Active Account
                                </span>
                            </div>
                        </div>

                        <hr class="text-muted">

                        <div class="row g-2 small">
                            <div class="col-4 text-muted">Email:</div>
                            <div class="col-8 fw-semibold">{{ $user->email }}</div>

                            <div class="col-4 text-muted">Telegram ID:</div>
                            <div class="col-8 fw-semibold">
                                {{ $user->telegram_id ? $user->telegram_id : 'Not Linked Yet' }}
                            </div>

                            <div class="col-4 text-muted">Member Since:</div>
                            <div class="col-8">{{ $user->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Family & Invite Code Card -->
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                                <i class="bi bi-house-door fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">{{ $user->family->name ?? 'Default Household' }}</h5>
                                <span class="text-muted small">Family Workspace</span>
                            </div>
                        </div>

                        <hr class="text-muted">

                        <div class="bg-light p-3 rounded-3 text-center mb-3">
                            <div class="text-muted small mb-1">Family Invite Code</div>
                            <div class="h3 fw-bold font-monospace text-primary mb-0">
                                {{ $user->family->invite_code ?? 'N/A' }}
                            </div>
                        </div>
                        <p class="text-muted small text-center mb-0">
                            Share this code with your family members so they can join this household upon registration or via Telegram Bot!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

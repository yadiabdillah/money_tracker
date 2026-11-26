<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Money Tracker') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-sm border-0 p-5 text-center" style="max-width: 540px;">
        <div class="mb-3">
            <i class="bi bi-wallet2 text-primary" style="font-size: 3rem;"></i>
        </div>
        <h1 class="h3 fw-bold text-dark mb-2">Money Tracker</h1>
        <p class="text-muted mb-4">Laravel 11 Local Runtime Environment Active & Ready.</p>
        <div class="d-flex justify-content-center gap-2">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                <i class="bi bi-check-circle me-1"></i> PHP 8.3
            </span>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                <i class="bi bi-box me-1"></i> Laravel 11
            </span>
            <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2">
                <i class="bi bi-lightning me-1"></i> Bootstrap 5
            </span>
        </div>
    </div>
</body>
</html>

@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
<div class="text-center mb-4">
    <h2 class="h5 fw-bold text-dark mb-1">Welcome Back</h2>
    <p class="text-muted small">Enter your email and password to access your family ledger.</p>
</div>

<form id="login-form" method="POST" action="{{ route('login.store') }}" novalidate>
    @csrf

    <!-- Email Address -->
    <div class="mb-3">
        <label for="email" class="form-label fw-semibold text-secondary small">Email Address</label>
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0 text-muted">
                <i class="bi bi-envelope"></i>
            </span>
            <input 
                type="email" 
                class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                placeholder="name@example.com" 
                required 
                autocomplete="email" 
                autofocus
            >
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Password -->
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <label for="password" class="form-label fw-semibold text-secondary small mb-1">Password</label>
        </div>
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0 text-muted">
                <i class="bi bi-lock"></i>
            </span>
            <input 
                type="password" 
                class="form-control border-start-0 border-end-0 ps-0 @error('password') is-invalid @enderror" 
                id="password" 
                name="password" 
                placeholder="Enter your password" 
                required 
                autocomplete="current-password"
            >
            <button 
                class="btn border border-start-0 bg-white text-muted btn-password-toggle" 
                type="button" 
                id="toggle-password-btn" 
                title="Toggle password visibility"
            >
                <i class="bi bi-eye" id="toggle-password-icon"></i>
            </button>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Remember Me Checkbox -->
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label text-muted small" for="remember">
                Keep me signed in
            </label>
        </div>
    </div>

    <!-- Submit Button with Double-Submit Prevention -->
    <div class="mb-3">
        <button type="submit" id="login-submit-btn" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
            <span class="submit-text"><i class="bi bi-box-arrow-in-right me-1"></i> Sign In</span>
            <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
            <span class="loading-text d-none">Signing In...</span>
        </button>
    </div>
</form>

<div class="text-center mt-4 pt-2 border-top">
    <p class="text-muted small mb-0">
        Don't have an account yet? 
        <a href="{{ route('register') }}" class="text-primary fw-semibold text-decoration-none">Create an Account</a>
    </p>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle Password Visibility
        const toggleBtn = document.getElementById('toggle-password-btn');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggle-password-icon');

        if (toggleBtn && passwordInput && toggleIcon) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                toggleIcon.classList.toggle('bi-eye', !isPassword);
                toggleIcon.classList.toggle('bi-eye-slash', isPassword);
            });
        }

        // Double-Submit Prevention
        const loginForm = document.getElementById('login-form');
        const submitBtn = document.getElementById('login-submit-btn');

        if (loginForm && submitBtn) {
            loginForm.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitBtn.querySelector('.submit-text').classList.add('d-none');
                submitBtn.querySelector('.spinner-border').classList.remove('d-none');
                submitBtn.querySelector('.loading-text').classList.remove('d-none');
            });
        }
    });
</script>
@endpush

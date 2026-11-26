@extends('layouts.auth')

@section('title', 'Create Account')

@section('content')
<div class="text-center mb-4">
    <h2 class="h5 fw-bold text-dark mb-1">Join Money Tracker</h2>
    <p class="text-muted small">Register to track family expenses or create your household ledger.</p>
</div>

<form id="register-form" method="POST" action="{{ route('register.store') }}" novalidate>
    @csrf

    <!-- Full Name -->
    <div class="mb-3">
        <label for="name" class="form-label fw-semibold text-secondary small">Full Name</label>
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0 text-muted">
                <i class="bi bi-person"></i>
            </span>
            <input 
                type="text" 
                class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" 
                id="name" 
                name="name" 
                value="{{ old('name') }}" 
                placeholder="e.g. John Smith" 
                required 
                autocomplete="name" 
                autofocus
            >
            @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

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
            >
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Password -->
    <div class="mb-3">
        <label for="password" class="form-label fw-semibold text-secondary small mb-1">Password</label>
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0 text-muted">
                <i class="bi bi-lock"></i>
            </span>
            <input 
                type="password" 
                class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" 
                id="password" 
                name="password" 
                placeholder="Minimum 8 characters" 
                required 
                autocomplete="new-password"
            >
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-text text-muted small">Must be at least 8 characters long.</div>
    </div>

    <!-- Password Confirmation -->
    <div class="mb-3">
        <label for="password_confirmation" class="form-label fw-semibold text-secondary small mb-1">Confirm Password</label>
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0 text-muted">
                <i class="bi bi-shield-check"></i>
            </span>
            <input 
                type="password" 
                class="form-control border-start-0 ps-0" 
                id="password_confirmation" 
                name="password_confirmation" 
                placeholder="Re-enter your password" 
                required 
                autocomplete="new-password"
            >
        </div>
    </div>

    <!-- Family Setup Options (New Family vs Join Family) -->
    <div class="card bg-light border-0 rounded-3 p-3 mb-4">
        <label class="form-label fw-semibold text-dark small mb-2">
            <i class="bi bi-people me-1 text-primary"></i> Family Setup
        </label>

        <div class="d-flex gap-3 mb-3">
            <div class="form-check">
                <input 
                    class="form-check-input" 
                    type="radio" 
                    name="registration_type" 
                    id="type_new_family" 
                    value="new_family" 
                    {{ old('registration_type', 'new_family') === 'new_family' ? 'checked' : '' }}
                >
                <label class="form-check-label small fw-medium" for="type_new_family">
                    Create New Family
                </label>
            </div>
            <div class="form-check">
                <input 
                    class="form-check-input" 
                    type="radio" 
                    name="registration_type" 
                    id="type_join_family" 
                    value="join_family" 
                    {{ old('registration_type') === 'join_family' ? 'checked' : '' }}
                >
                <label class="form-check-label small fw-medium" for="type_join_family">
                    Join Existing Family
                </label>
            </div>
        </div>

        <!-- Section: Create New Family Name -->
        <div id="section_new_family" class="{{ old('registration_type', 'new_family') === 'new_family' ? '' : 'd-none' }}">
            <label for="family_name" class="form-label text-muted small">Family or Household Name</label>
            <input 
                type="text" 
                class="form-control bg-white @error('family_name') is-invalid @enderror" 
                id="family_name" 
                name="family_name" 
                value="{{ old('family_name') }}" 
                placeholder="e.g. Smith Household"
            >
            <div class="form-text text-muted small">You will become the Family Admin and receive an invite code to share.</div>
            @error('family_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Section: Join Existing Family with Invite Code -->
        <div id="section_join_family" class="{{ old('registration_type') === 'join_family' ? '' : 'd-none' }}">
            <label for="invite_code" class="form-label text-muted small">Family Invite Code (8 characters)</label>
            <input 
                type="text" 
                class="form-control bg-white font-monospace text-uppercase @error('invite_code') is-invalid @enderror" 
                id="invite_code" 
                name="invite_code" 
                value="{{ old('invite_code') }}" 
                placeholder="e.g. DEMOFAM1" 
                maxlength="20"
            >
            <div class="form-text text-muted small">Ask your Family Admin for the invite code to join their ledger.</div>
            @error('invite_code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Submit Button -->
    <div class="mb-3">
        <button type="submit" id="register-submit-btn" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
            <span class="submit-text"><i class="bi bi-person-plus me-1"></i> Create Account</span>
            <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
            <span class="loading-text d-none">Creating Account...</span>
        </button>
    </div>
</form>

<div class="text-center mt-4 pt-2 border-top">
    <p class="text-muted small mb-0">
        Already have an account? 
        <a href="{{ route('login') }}" class="text-primary fw-semibold text-decoration-none">Sign In</a>
    </p>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeNew = document.getElementById('type_new_family');
        const typeJoin = document.getElementById('type_join_family');
        const sectionNew = document.getElementById('section_new_family');
        const sectionJoin = document.getElementById('section_join_family');
        const familyNameInput = document.getElementById('family_name');
        const inviteCodeInput = document.getElementById('invite_code');

        function toggleFamilySections() {
            if (typeNew.checked) {
                sectionNew.classList.remove('d-none');
                sectionJoin.classList.add('d-none');
            } else {
                sectionNew.classList.add('d-none');
                sectionJoin.classList.remove('d-none');
            }
        }

        if (typeNew && typeJoin) {
            typeNew.addEventListener('change', toggleFamilySections);
            typeJoin.addEventListener('change', toggleFamilySections);
        }

        // Auto-uppercase invite code
        if (inviteCodeInput) {
            inviteCodeInput.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });
        }

        // Double-Submit Prevention
        const registerForm = document.getElementById('register-form');
        const submitBtn = document.getElementById('register-submit-btn');

        if (registerForm && submitBtn) {
            registerForm.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitBtn.querySelector('.submit-text').classList.add('d-none');
                submitBtn.querySelector('.spinner-border').classList.remove('d-none');
                submitBtn.querySelector('.loading-text').classList.remove('d-none');
            });
        }
    });
</script>
@endpush

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes - Authentication & Core Navigation
|--------------------------------------------------------------------------
*/

// Public landing / redirection
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Guest-only routes (redirects authenticated users to /dashboard)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:3,1')
        ->name('register.store');
});

// Authenticated routes (redirects unauthenticated guests to /login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return view('dashboard', [
            'user' => auth()->user()->load('family'),
        ]);
    })->name('dashboard');
});

<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Display the login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $status = $this->authService->attemptLogin(
            $request->input('email'),
            $request->input('password'),
            $request->boolean('remember')
        );

        if ($status === AuthService::AUTH_DEACTIVATED) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'email' => 'Your account has been deactivated. Please contact your family administrator.',
                ]);
        }

        if ($status === AuthService::AUTH_INVALID_CREDENTIALS) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'email' => 'These credentials do not match our records.',
                ]);
        }

        // Authentication successful: Regenerate session to prevent fixation attacks
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Welcome back, ' . auth()->user()->name . '!');
    }

    /**
     * Display the registration form.
     */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = $this->authService->registerUser($request->validated());

        // Automatically authenticate the newly registered user
        auth()->login($user);

        // Regenerate session upon login
        $request->session()->regenerate();

        $welcomeMessage = $user->role === 'admin'
            ? 'Account created successfully! Your family invite code is ' . $user->family->invite_code . '.'
            : 'Welcome to the family! You have successfully registered.';

        return redirect()->route('dashboard')
            ->with('success', $welcomeMessage);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout($request);

        return redirect()->route('login')
            ->with('status', 'You have been successfully logged out.');
    }
}

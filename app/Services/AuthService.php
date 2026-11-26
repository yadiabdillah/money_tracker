<?php

namespace App\Services;

use App\Models\User;
use App\Models\Family;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class AuthService
{
    /**
     * Status constants for authentication attempt.
     */
    public const AUTH_SUCCESS = 'success';
    public const AUTH_INVALID_CREDENTIALS = 'invalid_credentials';
    public const AUTH_DEACTIVATED = 'deactivated';

    /**
     * Attempt to authenticate a user with specific outcome diagnostics.
     *
     * @param string $email
     * @param string $password
     * @param bool $remember
     * @return string Status code: AUTH_SUCCESS | AUTH_INVALID_CREDENTIALS | AUTH_DEACTIVATED
     */
    public function attemptLogin(string $email, string $password, bool $remember = false): string
    {
        $normalizedEmail = strtolower(trim($email));
        $user = User::where('email', $normalizedEmail)->first();

        // 1. Check user existence and verify password
        if (!$user || !Hash::check($password, $user->password)) {
            Log::warning('Failed login attempt: invalid credentials', [
                'email' => $normalizedEmail,
                'ip' => request()->ip(),
            ]);
            return self::AUTH_INVALID_CREDENTIALS;
        }

        // 2. Check if the user account is active
        if (!$user->is_active) {
            Log::warning('Blocked login attempt: deactivated account', [
                'user_id' => $user->id,
                'email' => $normalizedEmail,
                'ip' => request()->ip(),
            ]);
            return self::AUTH_DEACTIVATED;
        }

        // 3. Log user in via Laravel Auth session guard
        Auth::login($user, $remember);

        Log::info('Successful user login', [
            'user_id' => $user->id,
            'email' => $normalizedEmail,
            'ip' => request()->ip(),
        ]);

        return self::AUTH_SUCCESS;
    }

    /**
     * Register a new user and manage family onboarding inside a database transaction.
     *
     * @param array $data Validated input data from RegisterRequest
     * @return User
     * @throws \Throwable
     */
    public function registerUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $familyId = null;
            $role = 'member';

            if ($data['registration_type'] === 'new_family') {
                // Generate a unique 8-character invite code
                $inviteCode = Family::generateInviteCode();

                $family = Family::create([
                    'name' => trim($data['family_name']),
                    'invite_code' => $inviteCode,
                ]);

                $familyId = $family->id;
                $role = 'admin'; // Creator of the family is granted the admin role
            } else {
                // Lookup existing family by invite code
                $inviteCode = strtoupper(trim($data['invite_code']));
                $family = Family::where('invite_code', $inviteCode)->firstOrFail();

                $familyId = $family->id;
                $role = 'member'; // Members joining via invite code are assigned the member role
            }

            // Create the new User
            $user = User::create([
                'family_id' => $familyId,
                'name' => trim($data['name']),
                'email' => strtolower(trim($data['email'])),
                'password' => $data['password'], // Auto-hashed via User model $casts
                'role' => $role,
                'is_active' => true,
            ]);

            Log::info('New user registered successfully', [
                'user_id' => $user->id,
                'family_id' => $familyId,
                'role' => $role,
                'registration_type' => $data['registration_type'],
            ]);

            return $user;
        });
    }

    /**
     * Terminate the authenticated session and purge tokens.
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request): void
    {
        $userId = Auth::id();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out successfully', [
            'user_id' => $userId,
            'ip' => $request->ip(),
        ]);
    }
}

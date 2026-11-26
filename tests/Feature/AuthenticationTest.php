<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Family;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sign In');
    }

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Create Account');
    }

    public function test_user_can_authenticate_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@smithfamily.local',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_user_cannot_authenticate_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@smithfamily.local',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_register_with_new_family(): void
    {
        $email = 'newuser_' . time() . '@example.com';
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'registration_type' => 'new_family',
            'family_name' => 'The Test Family',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('families', [
            'name' => 'The Test Family',
        ]);
    }

    public function test_user_can_register_by_joining_existing_family(): void
    {
        $email = 'joinuser_' . time() . '@example.com';
        $response = $this->post('/register', [
            'name' => 'Join User',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'registration_type' => 'join_family',
            'invite_code' => 'DEMOFAM1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role' => 'member',
        ]);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::where('email', 'admin@smithfamily.local')->first();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}

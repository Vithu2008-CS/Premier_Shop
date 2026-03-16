<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'StrongP@ssw0rd123!',
            'password_confirmation' => 'StrongP@ssw0rd123!',
            'postal_code' => 'SW1A 1AA',
            'dob' => '2000-01-01',
        ]);

        $response->assertRedirect(route('register.verify'));

        $this->assertGuest();

        $otp = session('registration_otp');

        $verifyResponse = $this->post('/register/verify', [
            'otp' => $otp,
        ]);

        $this->assertAuthenticated();
        $verifyResponse->assertRedirect('/');
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_and_login_flow()
    {
        $registerResponse = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'role_type' => 'User'
        ]);

        $registerResponse->assertStatus(201)
                         ->assertJson([
                             'success' => true,
                             'message' => 'Registration successful'
                         ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => 'testuser@example.com',
            'password' => 'password',
        ]);

        $loginResponse->assertStatus(200)
                      ->assertJson([
                          'success' => true,
                          'message' => 'Login successful'
                      ]);
    }

 

    public function test_user_can_logout_and_fetch_profile()
    {
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'email' => 'testuser@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.token');

        $profileResponse = $this->withHeaders(['Authorization' => "Bearer $token"])
                                ->getJson('/api/profile');

        $profileResponse->assertStatus(200)
                        ->assertJson([
                            'success' => true,
                            'message' => 'Profile fetched successfully',
                        ]);

        $logoutResponse = $this->withHeaders(['Authorization' => "Bearer $token"])
                               ->postJson('/api/logout');

        $logoutResponse->assertStatus(200)
                       ->assertJson([
                           'success' => true,
                           'message' => 'Logout successful'
                       ]);
    }
}

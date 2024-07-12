<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthApiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_successful()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'role_type' => 'User'
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Registration successful'
                 ]);
    }

    public function test_register_validation_error()
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'role_type' => 'InvalidRole'
        ]);

        $response->assertStatus(422);
    }

    public function test_login_successful()
    {
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'testuser@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Login successful'
                 ]);
    }

    public function test_login_invalid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'invalid@example.com',
            'password' => 'invalid',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Invalid login credentials'
                 ]);
    }

    public function test_logout_successful()
    {
   
        $user = User::factory()->create();

        $token = $user->createToken('auth_token')->plainTextToken;
    
 
        $this->actingAs($user, 'api');
    
   
        $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->postJson('/api/logout');
    
      
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Logout successful'
                 ]);
    }

    



    public function test_profile_fetch_successful()
    {
    
        $user = User::factory()->create();
    
  
        $token = $user->createToken('auth_token')->plainTextToken;
    
     
        $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->getJson('/api/profile');
    
 
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Profile fetched successfully',
                     'data' => [
                         'user' => [
                             'id' => $user->id,
                             'name' => $user->name,
                             'email' => $user->email,
                         ]
                     ]
                 ]);
    }
    

    public function test_get_users_with_roles()
    {
    
        $user = User::factory()->create();
   
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->actingAs($user, 'api');
    
     
        $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->getJson('/api/users-with-roles');
    
        
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);
    }
    
}

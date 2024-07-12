<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AuthApiController extends Controller
{
    private function successResponse($data, $message = null, $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    private function errorResponse($message, $statusCode, $errors = [])
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }

    public function register(Request $request)
    {
        try {
            $startTime = microtime(true);
            
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role_type' => 'required|string|in:Admin,User'
            ]);
    
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_type' => $request->role_type, 
            ]);

            $executionTime = microtime(true) - $startTime;
            Log::info('User registered successfully.', ['user_id' => $user->id, 'execution_time' => $executionTime]);

            return $this->successResponse(['user' => $user], 'Registration successful', 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed during registration.', ['errors' => $e->errors()]);
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            Log::error('Error during user registration.', ['exception' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // Login method
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                Log::info('Invalid login attempt.', ['email' => $request->email]);
                return $this->errorResponse('Invalid login credentials', 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            Log::info('User logged in successfully.', ['user_id' => $user->id]);
            return $this->successResponse(['token' => $token, 'user' => $user], 'Login successful', 200);
        } catch (\Exception $e) {
            Log::error('Error during user login.', ['exception' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            Log::info('User logged out successfully.', ['user_id' => $request->user()->id]);
            return $this->successResponse([], 'Logout successful', 200);
        } catch (\Exception $e) {
            Log::error('Error during user logout.', ['exception' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            Log::info('Profile fetched successfully.', ['user_id' => $request->user()->id]);
            return $this->successResponse(['user' => $request->user()], 'Profile fetched successfully', 200);
        } catch (\Exception $e) {
            Log::error('Error fetching profile.', ['exception' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    // Get users with roles
    public function getUsersWithRoles()
    {
        try {
            $users = User::leftJoin('roles', 'users.role_type', '=', 'roles.name')
                         ->select('users.*', 'roles.id as role_Id')
                         ->get();

            Log::info('Fetched users with roles successfully.');
            return response()->json([
                'success' => true,
                'data' => ['users' => $users],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching users with roles.', ['exception' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function dashboard()
    {
        return view('dashboard');
    }
}

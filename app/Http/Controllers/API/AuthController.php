<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    use ApiResponse; 

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string|max:50|unique:users,username',
                'name' => 'required|string|max:100',
                'password' => 'required|string|min:8|max:60',
                'role' => 'sometimes|string|in:admin,user',
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $validated['role'] = $validated['role'] ?? 'user';

            $user = User::create($validated);
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->sendResponse([
                'user' => $user,
                'token' => $token,
            ], 'User registered successfully', 201);
        } catch (ValidationException $e) {
            return $this->sendError('Validation failed', 422, $e->errors());
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('username', $validated['username'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return $this->sendError('Invalid username or password', 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->sendResponse([
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'name' => $user->name,
                    'role' => $user->role,
                ],
                'token' => $token,
            ], 'Login successful', 200);
        } catch (ValidationException $e) {
            return $this->sendError('Validation failed', 422, $e->errors());
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return $this->sendResponse(null, 'Logout successful', 200);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
}
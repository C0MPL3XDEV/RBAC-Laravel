<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse {
        $validated = $request->validate([
            'name' => 'string|required',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('api_token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response()->json($response, 201);
    }

    public function login(Request $request): JsonResponse {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'roles' => $user->getRoleNames(), // Get user Roles
            'permissions' => $user->getAllPermissions()->pluck('name'), // Get user Permissions
        ], 201);
    }

    public function logout(Request $request): JsonResponse {
        $request->user()->tokens()->delete();

        $response = [
            'message' => 'Logged out'
        ];

        return response()->json($response, 200);
    }

    public function me(Request $request): JsonResponse {
        return response()->json($request->user());
    }
}

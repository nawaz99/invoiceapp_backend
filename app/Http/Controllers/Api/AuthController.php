<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // ✅ Register new user + return JWT token
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => bcrypt($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token, $user);
    }

    // ✅ Login user + return JWT token
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = auth()->user();

        return $this->respondWithToken($token, $user);
    }

    // ✅ Logout user (invalidate token)
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Logged out successfully']);
    }

    // ✅ Refresh token
    public function refresh()
    {
        $newToken = JWTAuth::refresh(JWTAuth::getToken());
        return $this->respondWithToken($newToken, auth()->user());
    }

    // ✅ Get current authenticated user
    public function me()
    {
        return response()->json(auth()->user());
    }

    // ✅ Helper to format token response
    private function respondWithToken($token, $user)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
            'user'         => $user
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());
        $user->assignRole($request->input('role', 'Patient'));

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['user' => $user->load('roles'), 'token' => $token], 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        if ($user->status !== 'active') {
            return response()->json(['message' => 'Account is inactive'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        activity('auth')->causedBy($user)->event('login')->log('User logged in');

        return response()->json(['user' => $user->load('roles'), 'token' => $token]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('roles', 'permissions'));
    }

    public function logout(Request $request)
    {
        activity('auth')->causedBy($request->user())->event('logout')->log('User logged out');
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}

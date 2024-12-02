<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public $app;

    public function __construct(AuthService $authService)
    {
        $this->app = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->app->createUser($request->only('name', 'email', 'password'));

        $token = $this->app->createToken($user);

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->app->authenticateUser($request->only('email', 'password'));

        if (!$user) {
            return response()->json(['message' => 'Invalid login details'], 401);
        }

        $token = $this->app->createToken($user);

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    public function logout(Request $request)
    {
        $res = $this->app->revokeToken($request->user());
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function logoutAllDevice(Request $request)
    {
        $res = $this->app->revokeAllToken($request->user());
        return response()->json(['message' => 'Successfully logged out from all devices']);
    }
}

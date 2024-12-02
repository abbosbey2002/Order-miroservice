<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Отозвать текущий токен пользователя.
     *
     * @return void
     */
    public function revokeToken($user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Отозвать все токены пользователей..
     *
     * @return void
     */
    public function revokeAllToken($user): void
    {
        $user->tokens()->delete();
    }

    /**
     * Вход пользователя
     *
     * @param array $data
     * @return User
     */
    public function authenticateUser(array $data): ?User
    {
        if (Auth::attempt($data)) {
            $user = User::where('email', $data['email'])->firstOrFail();
            return $user;
        }

        return null;
    }

    /**
     * 'Создание пользователя'
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Создание токенa
     *
     * @param [type] $user
     * @return void
     */
    public function createToken($user)
    {
        $token = $user->createToken('auth_token')->plainTextToken;

        return $token;
    }
}

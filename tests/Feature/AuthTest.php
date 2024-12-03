<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест на успешность регистрации
     */
    public function test_success_register()
    {
        $data = [
            'name' => 'Viktor',
            'email' => 'exapletest123456@gmail.com',
            'password' => 'password1345628',
            'password_confirmation' => 'password1345628',
        ];

        $response = $this->postJson('/api/register', $data);

        $response
            ->assertStatus(200)
            ->assertJson([
                'access_token' => true,
                'token_type' => 'Bearer',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'exapletest123456@gmail.com',
        ]);
    }
     /**
     * Тест ошибки регистра
     */
    public function test_error_register()
    {
        $data = [
            'name' => '',
            'email' => 'non email',
            'password' => 'short',
        ];

        $response = $this->postJson('/api/register', $data);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}
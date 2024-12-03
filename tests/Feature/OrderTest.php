<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тестируем метод для получения заказов.
     */
    public function test_can_fetch_orders()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        // Создаём заказы
        $order1 = Order::factory()->create(['user_email' => $user->email]);
        $order2 = Order::factory()->create(['user_email' => $user->email]);

        // Отправляем запрос
        $response = $this->getJson(route('orders.index'));

        // Проверяем, что в ответе есть заказы
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['user_email' => $user->email]);
        $response->assertJsonCount(2, 'orders');
    }

    /**
     * Тестируем метод для создания заказа.
     */
    public function test_can_store_order()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        // Подготавливаем данные
        $data = [
            'product_name' => 'Test Product',
            'price' => 100,
            'status' => 'pending',
        ];

        // Отправляем запрос
        $response = $this->postJson(route('orders.store'), $data);

        // Проверяем, что заказ был создан и принадлежит пользователю
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('orders', [
            'user_email' => $user->email,
            'product_name' => 'Test Product',
        ]);
    }

    /**
     * Тестируем метод для изменения заказа.
     */
    public function test_can_update_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_email' => $user->email]);
        $this->actingAs($user, 'sanctum');

        // Подготавливаем данные
        $data = [
            'product_name' => 'Updated Product',
            'price' => 150,
            'status' => 'processed',
        ];

        // Отправляем запрос для обновления заказа
        $response = $this->putJson(route('orders.update', $order), $data);

        // Проверяем, что заказ был обновлён
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'product_name' => 'Updated Product',
            'price' => 150,
            'status' => 'processed',
        ]);
    }

    /**
     * Тестируем метод для удаления заказа.
     */
    public function test_can_destroy_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_email' => $user->email]);
        $this->actingAs($user, 'sanctum');

        // Отправляем запрос на удаление заказа
        $response = $this->deleteJson(route('orders.destroy', $order));

        // Проверяем, что заказ был удалён
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    /**
     * Тестируем, что пользователь не может обновить заказ, который ему не принадлежит.
     */
    public function test_cannot_update_other_users_order()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $order = Order::factory()->create(['user_email' => $otherUser->email]);
        $this->actingAs($user, 'sanctum');

        // Подготавливаем данные
        $data = [
            'product_name' => 'Updated Product',
            'price' => 150,
            'status' => 'processed',
        ];

        // Отправляем запрос для обновления заказа
        $response = $this->putJson(route('orders.update', $order), $data);

        // Проверяем, что пользователь не может обновить чужой заказ
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonFragment(['message' => 'This order does not belong to you.']);
    }

    /**
     * Тестируем, что пользователь не может удалить заказ, который ему не принадлежит.
     */
    public function test_cannot_destroy_other_users_order()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $order = Order::factory()->create(['user_email' => $otherUser->email]);
        $this->actingAs($user, 'sanctum');

        // Отправляем запрос на удаление заказа
        $response = $this->deleteJson(route('orders.destroy', $order));

        // Проверяем, что пользователь не может удалить чужой заказ
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonFragment(['message' => 'This order does not belong to you.']);
    }
}

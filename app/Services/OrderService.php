<?php

namespace App\Services;

use App\Events\OrderEvent;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Создать новый заказ.
     * @param array $data
      * @return \App\Models\Order
     */
    public function createOrder(array $data): Order
    {
        $order = Order::create($data);

        event(new OrderEvent($order));

        return $order;
    }

    /**
     * Обновить существующий заказ.
     *
     * @param int $id
     * @param array $data
      * @return \App\Models\Order
     */
    public function updateOrder(int $id, array $data): Order
    {
        $order = Order::findOrFail($id);
        $order->update($data);
        return $order;
    }

    /**
     * Получить заказ по ID.
     * @param int $id
      * @return \App\Models\Order
     */
    public function getOrderById(int $id): Order
    {
        return Order::findOrFail($id);
    }

    /**
     * Удалить заказ по ID.
     *
     * @param int $id
     * @return  bool
     */
    public function destroyOrder(int $id): bool
    {
        $order = Order::findOrFail($id);
        return $order->delete();
    }

    /**
     * Получить заказов пользователей
     *
     * @param string $email
     * @param string $status
     * @return array $orders
     */
    public function getOrdersByUser(string $email, ?string $status)
    {
        $query = Order::where('user_email', $email);
        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return $orders;
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    /**
     * OrderService to controller  function
     *
     * @return void
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $userEmail = auth()->user()->email;
        $status = $request->query('status');
        $orders = $this->orderService->getOrdersByUser($userEmail, $status);
        return response()->json(['orders' => $orders], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $data = $request->all();
        $data['user_email'] = auth()->user()->email;
        $order = $this->orderService->createOrder($data);

        return response()->json($order, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        if ($this->checkUserAccess($order)) {
            $order = $this->orderService->updateOrder($order->id, $request->only(['product_name', 'price', 'status']));
            return response()->json($order, 200);
        } else {
            return response()->json(['message' => 'This order does not belong to you.'], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);

            if (!$this->checkUserAccess($order)) {
                return response()->json(['message' => 'This order does not belong to you.'], 403);
            }

            // Удаление заказа через сервис
            $this->orderService->destroyOrder($order->id);

            return response()->json(['message' => 'Order deleted successfully.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Order not found!'], 404);
        }
    }

    /**
     * Проверьте права пользователя.
     *
     * @param  \App\Models\Order  $order
     * @return bool
     */
    public function checkUserAccess(Order $order): bool
    {
        return $order->user_email === auth()->user()->email;
    }
}
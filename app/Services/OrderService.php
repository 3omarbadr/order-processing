<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Jobs\ProcessOrderPayment;
use App\Models\Order;
use App\Models\User;

class OrderService
{
    /**
     * Create a new order
     *
     * @return Order
     */
    public function createOrder(): Order
    {
        $user = User::first() ?? User::factory()->create();
        $amount = rand(100, 10000) / 100;

        $order = Order::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'status' => OrderStatus::PENDING->value,
        ]);

        ProcessOrderPayment::dispatch($order->id);

        return $order;
    }

    /**
     * Get an order by ID
     *
     * @param int $id
     * @return Order
     */
    public function getOrder(int $id): Order
    {
        return Order::findOrFail($id);
    }
}

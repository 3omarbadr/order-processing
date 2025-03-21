<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(): JsonResource
    {
        $order = $this->orderService->createOrder();

        return new OrderResource($order);
    }

    public function show(int $id): JsonResource
    {
        $order = $this->orderService->getOrder($id);

        return new OrderResource($order);
    }
}

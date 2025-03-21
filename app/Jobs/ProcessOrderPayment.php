<?php

namespace App\Jobs;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessOrderPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 30;

    public function __construct(
        public readonly int $orderId
    ) {
    }

    public function handle(PaymentService $paymentService): void
    {
        $order = Order::findOrFail($this->orderId);
        $paymentService->processPayment($order);
    }

    public function failed(Throwable $exception): void
    {
        Log::error("Job failed for order {$this->orderId}: {$exception->getMessage()}");

        $order = Order::find($this->orderId);

        if ($order && $order->status !== OrderStatus::FAILED) {
            $order->status = OrderStatus::FAILED;
            $order->save();
        }
    }
}

<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Process payment for an order
     *
     * @param Order $order
     * @return bool
     */
    public function processPayment(Order $order): bool
    {
        try {
            $order->update(['status' => OrderStatus::PROCESSING->value]);

            // Execute payment (simulation)
            $success = rand(0, 10) > 2; // 80% success rate

            $newStatus = $success ? OrderStatus::COMPLETED : OrderStatus::FAILED;
            $order->update(['status' => $newStatus->value]);

            $message = $success ? 'Payment processed successfully' : 'Payment failed';
            Log::info($message, [
                'order_id' => $order->id,
                'amount' => $order->amount
            ]);

            return $success;
        } catch (Exception $e) {
            Log::error('Payment processing failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'exception' => $e,
            ]);

            $order->update(['status' => OrderStatus::FAILED->value]);
            return false;
        }
    }
}

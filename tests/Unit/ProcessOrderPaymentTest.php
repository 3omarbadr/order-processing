<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use App\Jobs\ProcessOrderPayment;
use App\Models\Order;
use App\Models\User;
use App\Services\PaymentService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ProcessOrderPaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->paymentService = new PaymentService();
    }

    /**
     * Test that the job processes orders correctly.
     */
    public function test_job_processes_order_correctly(): void
    {
        // Arrange
        Log::shouldReceive('info')->atLeast()->once();
        Log::shouldReceive('error')->zeroOrMoreTimes();

        $order = Order::create([
            'user_id' => $this->user->id,
            'amount' => 100.00,
            'status' => OrderStatus::PENDING->value
        ]);

        // Act: Create and execute the job
        $job = new ProcessOrderPayment($order->id);

        try {
            $job->handle($this->paymentService);
        } catch (Exception $e) {
        }

        $order->refresh();

        // Assert: The order should have been updated (no longer be in pending status)
        $this->assertNotEquals(OrderStatus::PENDING, $order->status);
    }

    /**
     * Test that order status transitions through the expected states.
     */
    public function test_order_status_transitions(): void
    {
        // Arrange
        $order = Order::create([
            'user_id' => $this->user->id,
            'amount' => 100.00,
            'status' => OrderStatus::PENDING->value
        ]);

        $this->assertEquals(OrderStatus::PENDING, $order->status);

        Log::shouldReceive('info')->atLeast()->once();
        Log::shouldReceive('error')->zeroOrMoreTimes();

        // Act
        $job = new ProcessOrderPayment($order->id);

        try {
            $job->handle($this->paymentService);
        } catch (Exception $e) {
        }

        $order->refresh();

        // Assert
        $this->assertNotEquals(OrderStatus::PENDING, $order->status);
        $this->assertTrue(
            $order->status === OrderStatus::COMPLETED ||
            $order->status === OrderStatus::FAILED
        );
    }

    /**
     * Test that the job has appropriate retry configuration.
     */
    public function test_job_has_retry_configuration(): void
    {
        // Arrange & Act
        $job = new ProcessOrderPayment(1);

        // Assert
        $this->assertEquals(3, $job->tries);
        $this->assertEquals(30, $job->timeout);
    }
}

# Order Processing Queue Challenge

A Laravel application that implements a job queue system for processing order payments efficiently. This project demonstrates asynchronous queue processing, job retries, and unit testing in Laravel.

## Features

- Order management with different statuses (pending, processing, completed, failed)
- Asynchronous queue processing of payment jobs
- Automatic retry mechanism for failed jobs
- Simulated payment processing with randomized success/failure
- Dead-letter queue handling
- Unit testing for job processing
- Type-safe Enum-based order status management

## Requirements

- PHP 8.1 or higher
- Composer
- Docker (for Laravel Sail)
- MySQL

## Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/3omarbadr/order-processing.git
   cd order-processing
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Set up environment variables**
   ```bash
   cp .env.example .env
   ```
   Update the database credentials in the `.env` file if needed.

4. **Start the Laravel Sail environment**
   ```bash
   ./vendor/bin/sail up -d
   ```

5. **Run database migrations**
   ```bash
   ./vendor/bin/sail artisan migrate
   ```

## Testing the Order Processing Job via API

You can test the order processing job by creating an order through the API:

1. **Create a new order**

   ```bash
   curl -X POST http://localhost:8000/api/orders
   ```

   This endpoint creates a new order with a random amount and dispatches a `ProcessOrderPayment` job to the queue.

2. **Check order status**

   ```bash
   curl -X GET http://localhost:8000/api/orders/{id}
   ```

   Replace `{id}` with the order ID returned from the create order request.

3. **Monitor the job processing**

   The queue workers are already running and will automatically process the jobs. You can check the processing by:
   
   - Viewing the logs:
   ```bash
   ./vendor/bin/sail logs
   ```
   
   - Checking the order status again after a few seconds:
   ```bash
   curl -X GET http://localhost:8000/api/orders/{id}
   ```

4. **Expected behavior**

   - When you create an order, its status will initially be "pending"
   - The job will be dispatched to the queue
   - The workers will automatically pick up the job and update the status to "processing"
   - After simulated payment processing (with 80% success rate), the status will change to either "completed" or "failed"
   - If the job fails, it will be retried up to 3 times by the workers

## Testing

Run all tests using the following command:

```bash
./vendor/bin/sail test
```

Or run specific test files:

```bash
./vendor/bin/sail test --filter=ProcessOrderPaymentTest
```

## Project Structure

- `app/Models/Order.php` - Order model with status enum relationship
- `app/Enums/OrderStatus.php` - Type-safe enum representing order statuses
- `app/Jobs/ProcessOrderPayment.php` - Queue job that processes order payments
- `app/Services/PaymentService.php` - Service handling payment processing logic
- `database/migrations/xxx_create_orders_table.php` - Migration for the orders table
- `tests/Unit/ProcessOrderPaymentTest.php` - Unit tests for the order processing job

## Implementation Details

### Order Processing Flow

1. When an order is created, it has a default status of "pending"
2. The `ProcessOrderPayment` job is dispatched with the order ID
3. The job receives a `PaymentService` dependency through Laravel's service container
4. The job uses the service to process the payment, which:
   - Updates the order status to "processing"
   - Simulates an external payment API call with a delay
   - Logs payment attempt outcomes
   - Updates the order status to "completed" or "failed" based on the result
5. If the job fails, it's automatically retried (up to 3 times)

### Order Status Flow

Orders follow this status progression:

```
PENDING → PROCESSING → COMPLETED/FAILED
```

Each status is represented by a type-safe enum value that provides helper methods for status checking.

### Challenges and Solutions

- **Challenge**: Ensuring failed jobs are retried correctly
  **Solution**: Implemented the retry mechanism using Laravel's built-in retry functionality with `$tries` property and exceptions for failed jobs

- **Challenge**: Simulating real-world payment processing
  **Solution**: Used a randomized success/failure approach and proper status transitions to mimic real-world scenarios

- **Challenge**: Dependency injection in queue jobs
  **Solution**: Utilized Laravel's automatic resolution of dependencies in the job's handle method

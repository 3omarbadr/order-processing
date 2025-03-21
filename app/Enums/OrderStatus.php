<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    /**
     * Get a human-readable label for the status.
     *
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        };
    }

    /**
     * Get the CSS class for the status.
     *
     * @return string
     */
    public function cssClass(): string
    {
        return match($this) {
            self::PENDING => 'bg-yellow-100 text-yellow-800',
            self::PROCESSING => 'bg-blue-100 text-blue-800',
            self::COMPLETED => 'bg-green-100 text-green-800',
            self::FAILED => 'bg-red-100 text-red-800',
        };
    }

    /**
     * Check if the status is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Check if the status is processing.
     *
     * @return bool
     */
    public function isProcessing(): bool
    {
        return $this === self::PROCESSING;
    }

    /**
     * Check if the status is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    /**
     * Check if the status is failed.
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this === self::FAILED;
    }

    /**
     * Get all available statuses as an array.
     *
     * @return array
     */
    public static function toArray(): array
    {
        return [
            self::PENDING->value => self::PENDING->label(),
            self::PROCESSING->value => self::PROCESSING->label(),
            self::COMPLETED->value => self::COMPLETED->label(),
            self::FAILED->value => self::FAILED->label(),
        ];
    }
} 
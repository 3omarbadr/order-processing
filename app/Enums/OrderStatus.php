<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

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
}

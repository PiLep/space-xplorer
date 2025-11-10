<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when a job times out.
 */
class JobTimeoutException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $message = 'Job timed out', int $code = 504, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

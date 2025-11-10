<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when an API request fails.
 */
class ApiRequestException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $message = 'API request failed', int $code = 502, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

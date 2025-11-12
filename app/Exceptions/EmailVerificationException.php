<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when email verification operations fail.
 */
class EmailVerificationException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $message = 'Email verification failed', int $code = 400, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}


<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when storage operations fail (S3, local, etc.).
 */
class StorageException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $message = 'Storage operation failed', int $code = 500, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

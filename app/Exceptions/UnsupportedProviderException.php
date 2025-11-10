<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when an unsupported provider is requested.
 */
class UnsupportedProviderException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $provider, string $serviceType = 'service', int $code = 400, ?\Throwable $previous = null)
    {
        $message = "Unsupported {$serviceType} provider: {$provider}";
        parent::__construct($message, $code, $previous);
    }
}

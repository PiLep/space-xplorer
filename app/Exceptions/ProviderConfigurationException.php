<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when a provider is not configured or missing required configuration.
 */
class ProviderConfigurationException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $provider, string $message = '', int $code = 500, ?\Throwable $previous = null)
    {
        $message = $message ?: "Provider '{$provider}' is not configured or missing API key.";
        parent::__construct($message, $code, $previous);
    }
}

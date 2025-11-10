<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when image generation fails.
 */
class ImageGenerationException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $message = 'Image generation failed', int $code = 500, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

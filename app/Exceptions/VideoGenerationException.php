<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when video generation fails.
 */
class VideoGenerationException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $message = 'Video generation failed', int $code = 500, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

<?php

use App\Exceptions\VideoGenerationException;

it('can be instantiated with default message and code', function () {
    $exception = new VideoGenerationException;

    expect($exception->getMessage())->toBe('Video generation failed')
        ->and($exception->getCode())->toBe(500);
});

it('can be instantiated with custom message', function () {
    $message = 'Custom video generation error';
    $exception = new VideoGenerationException($message);

    expect($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe(500);
});

it('can be instantiated with custom message and code', function () {
    $message = 'Custom video generation error';
    $code = 502;
    $exception = new VideoGenerationException($message, $code);

    expect($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe($code);
});

it('can be instantiated with previous exception', function () {
    $previous = new \Exception('Previous error');
    $exception = new VideoGenerationException('Video generation failed', 500, $previous);

    expect($exception->getPrevious())->toBe($previous);
});

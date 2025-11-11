<?php

use App\Exceptions\JobTimeoutException;

it('can be instantiated with default message and code', function () {
    $exception = new JobTimeoutException;

    expect($exception->getMessage())->toBe('Job timed out')
        ->and($exception->getCode())->toBe(504);
});

it('can be instantiated with custom message', function () {
    $message = 'Custom timeout message';
    $exception = new JobTimeoutException($message);

    expect($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe(504);
});

it('can be instantiated with custom message and code', function () {
    $message = 'Custom timeout message';
    $code = 500;
    $exception = new JobTimeoutException($message, $code);

    expect($exception->getMessage())->toBe($message)
        ->and($exception->getCode())->toBe($code);
});

it('can be instantiated with previous exception', function () {
    $previous = new \Exception('Previous error');
    $exception = new JobTimeoutException('Job timed out', 504, $previous);

    expect($exception->getPrevious())->toBe($previous);
});

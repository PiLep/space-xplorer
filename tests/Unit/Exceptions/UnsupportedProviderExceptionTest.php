<?php

use App\Exceptions\UnsupportedProviderException;

it('can be instantiated with provider name', function () {
    $exception = new UnsupportedProviderException('invalid_provider');

    expect($exception->getMessage())->toBe('Unsupported service provider: invalid_provider')
        ->and($exception->getCode())->toBe(400);
});

it('can be instantiated with provider and service type', function () {
    $exception = new UnsupportedProviderException('invalid_provider', 'image');

    expect($exception->getMessage())->toBe('Unsupported image provider: invalid_provider')
        ->and($exception->getCode())->toBe(400);
});

it('can be instantiated with custom code', function () {
    $exception = new UnsupportedProviderException('invalid_provider', 'service', 404);

    expect($exception->getMessage())->toBe('Unsupported service provider: invalid_provider')
        ->and($exception->getCode())->toBe(404);
});

it('can be instantiated with previous exception', function () {
    $previous = new \Exception('Previous error');
    $exception = new UnsupportedProviderException('invalid_provider', 'service', 400, $previous);

    expect($exception->getPrevious())->toBe($previous);
});

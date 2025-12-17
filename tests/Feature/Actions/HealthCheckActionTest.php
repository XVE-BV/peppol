<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Xve\LaravelPeppol\Actions\HealthCheckAction;
use Xve\LaravelPeppol\Exceptions\ConnectionException;
use Xve\LaravelPeppol\Support\HealthStatus;

beforeEach(function () {
    config()->set('peppol-gateway.base_url', 'https://api.example.com');
    config()->set('peppol-gateway.client_id', 'test-client');
    config()->set('peppol-gateway.client_secret', 'test-secret');
});

it('executes health check successfully', function () {
    Http::fake([
        'api.example.com/api/system/health' => Http::response([
            'ok' => true,
            'status' => 200,
            'base_url' => 'https://flowin.example.com',
            'mtls_configured' => true,
        ]),
    ]);

    $action = app(HealthCheckAction::class);
    $result = $action->execute();

    expect($result)->toBeInstanceOf(HealthStatus::class)
        ->and($result->ok)->toBeTrue()
        ->and($result->status)->toBe(200)
        ->and($result->mtlsConfigured)->toBeTrue();
});

it('returns unhealthy status', function () {
    Http::fake([
        'api.example.com/api/system/health' => Http::response([
            'ok' => false,
            'status' => 502,
            'error' => 'Flowin connection failed',
        ], 502),
    ]);

    $action = app(HealthCheckAction::class);
    $result = $action->execute();

    expect($result->ok)->toBeFalse()
        ->and($result->error)->toBe('Flowin connection failed');
});

it('throws connection exception on network failure', function () {
    Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('Connection refused'));

    $action = app(HealthCheckAction::class);
    $action->execute();
})->throws(ConnectionException::class, 'Could not connect to Peppol Gateway');

it('sends correct headers', function () {
    Http::fake([
        'api.example.com/api/system/health' => Http::response(['ok' => true, 'status' => 200]),
    ]);

    $action = app(HealthCheckAction::class);
    $action->execute();

    Http::assertSent(function ($request) {
        return $request->hasHeader('X-Api-Client-Id', 'test-client')
            && $request->hasHeader('Authorization', 'Bearer test-secret')
            && $request->hasHeader('Accept', 'application/json');
    });
});

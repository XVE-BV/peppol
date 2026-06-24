<?php

declare(strict_types=1);

use Xve\LaravelPeppol\Actions\HealthCheckAction;
use Xve\LaravelPeppol\Exceptions\AuthenticationException;
use Xve\LaravelPeppol\Exceptions\ConnectionException;
use Xve\LaravelPeppol\Exceptions\InvalidActionClass;
use Xve\LaravelPeppol\Services\PeppolGatewayService;
use Xve\LaravelPeppol\Support\Config;

it('returns base url from config', function (): void {
    config()->set('peppol-gateway.base_url', 'https://api.example.com/');

    expect(Config::getBaseUrl())->toBe('https://api.example.com');
});

it('trims trailing slash from base url', function (): void {
    config()->set('peppol-gateway.base_url', 'https://api.example.com///');

    expect(Config::getBaseUrl())->toBe('https://api.example.com');
});

it('throws exception when base url is missing', function (): void {
    config()->set('peppol-gateway.base_url');

    Config::getBaseUrl();
})->throws(ConnectionException::class, 'Missing API base URL');

it('returns client id from config', function (): void {
    config()->set('peppol-gateway.client_id', 'test-client-id');

    expect(Config::getClientId())->toBe('test-client-id');
});

it('throws exception when client id is missing', function (): void {
    config()->set('peppol-gateway.client_id');

    Config::getClientId();
})->throws(AuthenticationException::class, 'Missing API credentials');

it('returns client secret from config', function (): void {
    config()->set('peppol-gateway.client_secret', 'test-secret');

    expect(Config::getClientSecret())->toBe('test-secret');
});

it('throws exception when client secret is missing', function (): void {
    config()->set('peppol-gateway.client_secret');

    Config::getClientSecret();
})->throws(AuthenticationException::class, 'Missing API credentials');

it('returns timeout from config with default', function (): void {
    config()->set('peppol-gateway.timeout');

    expect(Config::getTimeout())->toBe(30);
});

it('returns custom timeout from config', function (): void {
    config()->set('peppol-gateway.timeout', 60);

    expect(Config::getTimeout())->toBe(60);
});

it('returns action class from config', function (): void {
    expect(Config::getActionClass('health_check', HealthCheckAction::class))
        ->toBe(HealthCheckAction::class);
});

it('returns action instance from config', function (): void {
    $action = Config::getAction('health_check', HealthCheckAction::class);

    expect($action)->toBeInstanceOf(HealthCheckAction::class);
});

it('falls back to base class when action not configured', function (): void {
    config()->set('peppol-gateway.actions.custom_action');

    expect(Config::getActionClass('custom_action', HealthCheckAction::class))
        ->toBe(HealthCheckAction::class);
});

it('throws exception for invalid action class', function (): void {
    config()->set('peppol-gateway.actions.health_check', stdClass::class);

    Config::getActionClass('health_check', HealthCheckAction::class);
})->throws(InvalidActionClass::class);

it('returns service instance', function (): void {
    $service = Config::service();

    expect($service)->toBeInstanceOf(PeppolGatewayService::class);
});

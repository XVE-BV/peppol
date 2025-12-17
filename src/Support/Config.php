<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Support;

use Xve\LaravelPeppol\Exceptions\AuthenticationException;
use Xve\LaravelPeppol\Exceptions\ConnectionException;
use Xve\LaravelPeppol\Exceptions\InvalidActionClass;
use Xve\LaravelPeppol\Services\PeppolGatewayService;

class Config
{
    public static function getBaseUrl(): string
    {
        $url = config('peppol-gateway.base_url');

        if (empty($url)) {
            throw ConnectionException::missingBaseUrl();
        }

        return rtrim($url, '/');
    }

    public static function getClientId(): string
    {
        $clientId = config('peppol-gateway.client_id');

        if (empty($clientId)) {
            throw AuthenticationException::missingCredentials();
        }

        return $clientId;
    }

    public static function getClientSecret(): string
    {
        $clientSecret = config('peppol-gateway.client_secret');

        if (empty($clientSecret)) {
            throw AuthenticationException::missingCredentials();
        }

        return $clientSecret;
    }

    public static function getTimeout(): int
    {
        return (int) (config('peppol-gateway.timeout') ?? 30);
    }

    /**
     * @template T
     *
     * @param  class-string<T>  $actionBaseClass
     * @return class-string<T>
     */
    public static function getActionClass(string $actionName, string $actionBaseClass): string
    {
        $actionClass = config("peppol-gateway.actions.{$actionName}") ?? $actionBaseClass;

        self::ensureValidActionClass($actionName, $actionBaseClass, $actionClass);

        return $actionClass;
    }

    /**
     * @template T
     *
     * @param  class-string<T>  $actionBaseClass
     * @return T
     */
    public static function getAction(string $actionName, string $actionBaseClass)
    {
        $actionClass = self::getActionClass($actionName, $actionBaseClass);

        return app($actionClass);
    }

    protected static function ensureValidActionClass(string $actionName, string $actionBaseClass, string $actionClass): void
    {
        if (! is_a($actionClass, $actionBaseClass, true)) {
            throw InvalidActionClass::make($actionName, $actionBaseClass, $actionClass);
        }
    }

    public static function service(): PeppolGatewayService
    {
        return app(PeppolGatewayService::class);
    }
}

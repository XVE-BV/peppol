<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Xve\LaravelPeppol\Exceptions\AuthenticationException;
use Xve\LaravelPeppol\Exceptions\ConnectionException;

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
        return (int) config('peppol-gateway.timeout', 30);
    }

    public static function getActionClass(string $actionName): string
    {
        return config("peppol-gateway.actions.{$actionName}");
    }

    public static function httpClient(): PendingRequest
    {
        return Http::baseUrl(self::getBaseUrl())
            ->timeout(self::getTimeout())
            ->withHeaders([
                'X-Api-Client-Id' => self::getClientId(),
                'Authorization' => 'Bearer '.self::getClientSecret(),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]);
    }
}

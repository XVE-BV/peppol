<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Exceptions;

class AuthenticationException extends PeppolGatewayException
{
    public static function invalidCredentials(): self
    {
        return new self('Invalid API credentials. Please check your client ID and secret.');
    }

    public static function missingCredentials(): self
    {
        return new self('Missing API credentials. Please set PEPPOL_GATEWAY_CLIENT_ID and PEPPOL_GATEWAY_CLIENT_SECRET in your .env file.');
    }
}

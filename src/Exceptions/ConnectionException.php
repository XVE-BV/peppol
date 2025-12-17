<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Exceptions;

class ConnectionException extends PeppolGatewayException
{
    public static function timeout(): self
    {
        return new self('Connection to Peppol Gateway timed out.');
    }

    public static function unreachable(): self
    {
        return new self('Could not connect to Peppol Gateway. Please check the API URL.');
    }

    public static function missingBaseUrl(): self
    {
        return new self('Missing API base URL. Please set PEPPOL_GATEWAY_URL in your .env file.');
    }
}

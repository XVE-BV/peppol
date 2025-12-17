<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Exceptions;

class InvoiceException extends PeppolGatewayException
{
    public static function notFound(string $id): self
    {
        return new self("Invoice with ID '{$id}' was not found.");
    }

    public static function sendFailed(string $reason): self
    {
        return new self("Failed to send invoice: {$reason}");
    }
}

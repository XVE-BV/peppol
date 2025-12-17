<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Exceptions;

class InvalidActionClass extends PeppolGatewayException
{
    public static function make(string $actionName, string $expectedClass, string $actualClass): self
    {
        return new self("Action '{$actionName}' must be an instance of {$expectedClass}, but {$actualClass} was configured.");
    }
}

<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Exceptions;

class ValidationException extends PeppolGatewayException
{
    protected array $errors = [];

    public static function fromResponse(array $errors): self
    {
        $exception = new self('Validation failed: '.json_encode($errors));
        $exception->errors = $errors;

        return $exception;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}

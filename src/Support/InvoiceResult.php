<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Support;

class InvoiceResult
{
    public function __construct(
        public readonly string $status,
        public readonly string $uuid,
    ) {}

    public static function fromResponse(array $data): self
    {
        return new self(
            status: $data['status'] ?? '',
            uuid: $data['uuid'] ?? '',
        );
    }
}

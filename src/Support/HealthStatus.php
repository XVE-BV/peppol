<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Support;

class HealthStatus
{
    public function __construct(
        public readonly bool $ok,
        public readonly int $status,
        public readonly ?string $baseUrl = null,
        public readonly ?bool $mtlsConfigured = null,
        public readonly ?string $error = null,
    ) {}

    public static function fromResponse(array $data): self
    {
        return new self(
            ok: $data['ok'] ?? false,
            status: $data['status'] ?? 0,
            baseUrl: $data['base_url'] ?? null,
            mtlsConfigured: $data['mtls_configured'] ?? null,
            error: $data['error'] ?? null,
        );
    }
}

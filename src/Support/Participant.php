<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Support;

class Participant
{
    public function __construct(
        public readonly string $participantId,
        public readonly string $vat,
        public readonly bool $capable,
        public readonly array $documentTypes = [],
        public readonly ?array $metadata = null,
    ) {}

    public static function fromResponse(array $data): self
    {
        return new self(
            participantId: $data['participant_id'] ?? '',
            vat: $data['vat'] ?? '',
            capable: $data['capable'] ?? false,
            documentTypes: $data['documentTypes'] ?? [],
            metadata: $data['metadata'] ?? null,
        );
    }
}

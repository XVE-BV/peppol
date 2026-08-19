<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Support;

class VatValidationResult
{
    /**
     * @param  list<string>  $missing  e.g. ['VAT_COMBINATION:standard|700100|21|', ...]
     */
    public function __construct(
        public readonly bool $accepted,
        public readonly array $missing,
    ) {}

    public static function fromResponse(array $data): self
    {
        return new self(
            accepted: (bool) ($data['accepted'] ?? false),
            missing: $data['missing'] ?? [],
        );
    }
}

<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Support;

class InvoiceStatus
{
    public function __construct(
        public readonly int $id,
        public readonly string $uuid,
        public readonly string $type,
        public readonly string $status,
        public readonly ?string $buyerVat = null,
        public readonly ?string $buyerReference = null,
        public readonly ?string $flowinId = null,
        public readonly ?string $total = null,
        public readonly ?string $currency = null,
        public readonly ?array $metadata = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    public static function fromResponse(array $data): self
    {
        $invoice = $data['invoice'] ?? $data;

        return new self(
            id: $invoice['id'] ?? 0,
            uuid: $invoice['uuid'] ?? '',
            type: $invoice['type'] ?? '',
            status: $invoice['status'] ?? '',
            buyerVat: $invoice['buyer_vat'] ?? null,
            buyerReference: $invoice['buyer_reference'] ?? null,
            flowinId: $invoice['flowin_id'] ?? null,
            total: $invoice['total'] ?? null,
            currency: $invoice['currency'] ?? null,
            metadata: $invoice['metadata'] ?? null,
            createdAt: $invoice['created_at'] ?? null,
            updatedAt: $invoice['updated_at'] ?? null,
        );
    }
}

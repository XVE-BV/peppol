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
        public readonly ?string $technicalStatus = null,
        public readonly ?string $lastUpdated = null,
        public readonly ?string $invoiceNumber = null,
        public readonly ?string $buyerPeppolId = null,
        public readonly ?string $buyerName = null,
        public readonly ?string $transmissionId = null,
        public readonly ?string $format = null,
        public readonly ?string $submittedAt = null,
        public readonly ?string $sentAt = null,
        public readonly ?string $acknowledgedAt = null,
        public readonly ?string $deliveredAt = null,
        /** @var list<array{code: string, message: string}> */
        public readonly array $errors = [],
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
            technicalStatus: $invoice['technical_status'] ?? null,
            lastUpdated: $invoice['last_updated'] ?? null,
            invoiceNumber: $invoice['invoice_number'] ?? null,
            buyerPeppolId: $invoice['buyer_peppol_id'] ?? null,
            buyerName: $invoice['buyer_name'] ?? null,
            transmissionId: $invoice['transmission_id'] ?? null,
            format: $invoice['format'] ?? null,
            submittedAt: $invoice['submitted_at'] ?? null,
            sentAt: $invoice['sent_at'] ?? null,
            acknowledgedAt: $invoice['acknowledged_at'] ?? null,
            deliveredAt: $invoice['delivered_at'] ?? null,
            errors: $invoice['errors'] ?? [],
        );
    }
}

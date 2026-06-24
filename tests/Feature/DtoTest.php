<?php

declare(strict_types=1);

use Xve\LaravelPeppol\Support\HealthStatus;
use Xve\LaravelPeppol\Support\InvoiceResult;
use Xve\LaravelPeppol\Support\InvoiceStatus;
use Xve\LaravelPeppol\Support\Participant;

describe('HealthStatus', function (): void {
    it('creates from response array', function (): void {
        $data = [
            'ok' => true,
            'status' => 200,
            'base_url' => 'https://api.example.com',
            'mtls_configured' => true,
        ];

        $health = HealthStatus::fromResponse($data);

        expect($health->ok)->toBeTrue()
            ->and($health->status)->toBe(200)
            ->and($health->baseUrl)->toBe('https://api.example.com')
            ->and($health->mtlsConfigured)->toBeTrue()
            ->and($health->error)->toBeNull();
    });

    it('handles error response', function (): void {
        $data = [
            'ok' => false,
            'status' => 502,
            'error' => 'Connection failed',
        ];

        $health = HealthStatus::fromResponse($data);

        expect($health->ok)->toBeFalse()
            ->and($health->status)->toBe(502)
            ->and($health->error)->toBe('Connection failed');
    });

    it('handles empty response', function (): void {
        $health = HealthStatus::fromResponse([]);

        expect($health->ok)->toBeFalse()
            ->and($health->status)->toBe(0);
    });
});

describe('Participant', function (): void {
    it('creates from response array', function (): void {
        $data = [
            'data' => [
                'id' => '8ea99b6a-c891-4f48-964e-208b49a19c93',
                'type' => 'peppolCustomerSearch',
                'attributes' => [
                    'customerReference' => '0208:0805374964',
                    'supportedDocumentFormats' => [
                        [
                            'rootNamespace' => 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
                            'localName' => 'Invoice',
                        ],
                    ],
                ],
            ],
        ];

        $participant = Participant::fromResponse($data);

        expect($participant->id)->toBe('8ea99b6a-c891-4f48-964e-208b49a19c93')
            ->and($participant->participantId)->toBe('0208:0805374964')
            ->and($participant->capable)->toBeTrue()
            ->and($participant->supportedDocumentFormats)->toHaveCount(1);
    });

    it('handles not capable participant', function (): void {
        $data = [
            'data' => [
                'id' => '8ea99b6a-c891-4f48-964e-208b49a19c93',
                'type' => 'peppolCustomerSearch',
                'attributes' => [
                    'customerReference' => '',
                    'supportedDocumentFormats' => [],
                ],
            ],
        ];

        $participant = Participant::fromResponse($data);

        expect($participant->capable)->toBeFalse()
            ->and($participant->supportedDocumentFormats)->toBe([]);
    });
});

describe('InvoiceResult', function (): void {
    it('creates from response array', function (): void {
        $data = [
            'status' => 'queued',
            'uuid' => '550e8400-e29b-41d4-a716-446655440000',
        ];

        $result = InvoiceResult::fromResponse($data);

        expect($result->status)->toBe('queued')
            ->and($result->uuid)->toBe('550e8400-e29b-41d4-a716-446655440000');
    });
});

describe('InvoiceStatus', function (): void {
    it('creates from response array with invoice wrapper', function (): void {
        $data = [
            'invoice' => [
                'id' => 1,
                'uuid' => '550e8400-e29b-41d4-a716-446655440000',
                'type' => 'invoice',
                'status' => 'delivered',
                'buyer_vat' => 'BE0123456789',
                'buyer_reference' => 'INV-001',
                'flowin_id' => 'FLOWIN-123',
                'total' => '121.00',
                'currency' => 'EUR',
                'created_at' => '2025-01-15T10:00:00Z',
                'updated_at' => '2025-01-15T12:00:00Z',
            ],
        ];

        $status = InvoiceStatus::fromResponse($data);

        expect($status->id)->toBe(1)
            ->and($status->uuid)->toBe('550e8400-e29b-41d4-a716-446655440000')
            ->and($status->type)->toBe('invoice')
            ->and($status->status)->toBe('delivered')
            ->and($status->buyerVat)->toBe('BE0123456789')
            ->and($status->flowinId)->toBe('FLOWIN-123')
            ->and($status->total)->toBe('121.00')
            ->and($status->currency)->toBe('EUR');
    });

    it('creates from flat response array', function (): void {
        $data = [
            'id' => 1,
            'uuid' => '550e8400-e29b-41d4-a716-446655440000',
            'type' => 'credit_note',
            'status' => 'rejected',
        ];

        $status = InvoiceStatus::fromResponse($data);

        expect($status->type)->toBe('credit_note')
            ->and($status->status)->toBe('rejected');
    });

    it('maps transmission detail fields from snake_case keys', function (): void {
        $data = [
            'invoice' => [
                'id' => 1,
                'uuid' => '550e8400-e29b-41d4-a716-446655440000',
                'type' => 'invoice',
                'status' => 'delivered',
                'invoice_number' => 'INV-2025-001',
                'buyer_peppol_id' => '0208:0805374964',
                'buyer_name' => 'Acme Corp',
                'transmission_id' => 'TRANS-ABC123',
                'format' => 'BIS3',
                'submitted_at' => '2025-01-15T10:00:00Z',
                'sent_at' => '2025-01-15T10:01:00Z',
                'acknowledged_at' => '2025-01-15T10:02:00Z',
                'delivered_at' => '2025-01-15T10:03:00Z',
                'errors' => [
                    ['code' => 'ERR_001', 'message' => 'Delivery failed'],
                ],
            ],
        ];

        $status = InvoiceStatus::fromResponse($data);

        expect($status->invoiceNumber)->toBe('INV-2025-001')
            ->and($status->buyerPeppolId)->toBe('0208:0805374964')
            ->and($status->buyerName)->toBe('Acme Corp')
            ->and($status->transmissionId)->toBe('TRANS-ABC123')
            ->and($status->format)->toBe('BIS3')
            ->and($status->submittedAt)->toBe('2025-01-15T10:00:00Z')
            ->and($status->sentAt)->toBe('2025-01-15T10:01:00Z')
            ->and($status->acknowledgedAt)->toBe('2025-01-15T10:02:00Z')
            ->and($status->deliveredAt)->toBe('2025-01-15T10:03:00Z')
            ->and($status->errors)->toBe([['code' => 'ERR_001', 'message' => 'Delivery failed']]);
    });

    it('defaults errors to empty array when key absent', function (): void {
        $data = [
            'id' => 1,
            'uuid' => '550e8400-e29b-41d4-a716-446655440000',
            'type' => 'invoice',
            'status' => 'delivered',
        ];

        $status = InvoiceStatus::fromResponse($data);

        expect($status->errors)->toBe([]);
    });
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Xve\LaravelPeppol\Actions\SendInvoiceAction;
use Xve\LaravelPeppol\Exceptions\AuthenticationException;
use Xve\LaravelPeppol\Exceptions\InvoiceException;
use Xve\LaravelPeppol\Exceptions\ValidationException;
use Xve\LaravelPeppol\Support\InvoiceResult;

beforeEach(function () {
    config()->set('peppol-gateway.base_url', 'https://api.example.com');
    config()->set('peppol-gateway.client_id', 'test-client');
    config()->set('peppol-gateway.client_secret', 'test-secret');
});

it('sends invoice successfully', function () {
    Http::fake([
        'api.example.com/api/invoices/json' => Http::response([
            'status' => 'queued',
            'uuid' => '550e8400-e29b-41d4-a716-446655440000',
        ], 202),
    ]);

    $action = app(SendInvoiceAction::class);
    $result = $action->execute([
        'type' => 'invoice',
        'id' => 'INV-001',
        'total' => 121.00,
        'currency' => 'EUR',
        'buyer_vat' => 'BE0123456789',
    ]);

    expect($result)->toBeInstanceOf(InvoiceResult::class)
        ->and($result->status)->toBe('queued')
        ->and($result->uuid)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('sends invoice data in request body', function () {
    Http::fake([
        'api.example.com/api/invoices/json' => Http::response([
            'status' => 'queued',
            'uuid' => '550e8400-e29b-41d4-a716-446655440000',
        ], 202),
    ]);

    $invoiceData = [
        'type' => 'invoice',
        'id' => 'INV-001',
        'total' => 121.00,
        'currency' => 'EUR',
        'buyer_vat' => 'BE0123456789',
        'metadata' => [
            'buyer_name' => 'Test Company',
        ],
    ];

    $action = app(SendInvoiceAction::class);
    $action->execute($invoiceData);

    Http::assertSent(function ($request) {
        return $request['type'] === 'invoice'
            && $request['id'] === 'INV-001'
            && $request['total'] === 121.00
            && $request['buyer_vat'] === 'BE0123456789';
    });
});

it('throws authentication exception on 401', function () {
    Http::fake([
        'api.example.com/api/invoices/json' => Http::response(['message' => 'Unauthorized'], 401),
    ]);

    $action = app(SendInvoiceAction::class);
    $action->execute(['type' => 'invoice', 'total' => 100, 'currency' => 'EUR']);
})->throws(AuthenticationException::class);

it('throws validation exception on 422', function () {
    Http::fake([
        'api.example.com/api/invoices/json' => Http::response([
            'message' => 'Validation failed',
            'errors' => ['type' => ['The type field is required']],
        ], 422),
    ]);

    $action = app(SendInvoiceAction::class);
    $action->execute([]);
})->throws(ValidationException::class);

it('throws invoice exception on other failures', function () {
    Http::fake([
        'api.example.com/api/invoices/json' => Http::response([
            'message' => 'Internal server error',
        ], 500),
    ]);

    $action = app(SendInvoiceAction::class);
    $action->execute(['type' => 'invoice', 'total' => 100, 'currency' => 'EUR']);
})->throws(InvoiceException::class, 'Failed to send invoice');

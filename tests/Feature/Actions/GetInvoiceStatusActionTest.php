<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Xve\LaravelPeppol\Actions\GetInvoiceStatusAction;
use Xve\LaravelPeppol\Exceptions\AuthenticationException;
use Xve\LaravelPeppol\Exceptions\ConnectionException;
use Xve\LaravelPeppol\Exceptions\InvoiceException;
use Xve\LaravelPeppol\Support\InvoiceStatus;

beforeEach(function () {
    config()->set('peppol-gateway.base_url', 'https://api.example.com');
    config()->set('peppol-gateway.client_id', 'test-client');
    config()->set('peppol-gateway.client_secret', 'test-secret');
});

it('gets invoice status successfully', function () {
    Http::fake([
        'api.example.com/api/invoices/550e8400-e29b-41d4-a716-446655440000/status' => Http::response([
            'invoice' => [
                'id' => 1,
                'uuid' => '550e8400-e29b-41d4-a716-446655440000',
                'type' => 'invoice',
                'status' => 'delivered',
                'buyer_vat' => 'BE0123456789',
                'flowin_id' => 'FLOWIN-123',
                'total' => '121.00',
                'currency' => 'EUR',
            ],
        ]),
    ]);

    $action = app(GetInvoiceStatusAction::class);
    $result = $action->execute('550e8400-e29b-41d4-a716-446655440000');

    expect($result)->toBeInstanceOf(InvoiceStatus::class)
        ->and($result->status)->toBe('delivered')
        ->and($result->uuid)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($result->flowinId)->toBe('FLOWIN-123');
});

it('gets invoice status with numeric id', function () {
    Http::fake([
        'api.example.com/api/invoices/123/status' => Http::response([
            'invoice' => [
                'id' => 123,
                'uuid' => '550e8400-e29b-41d4-a716-446655440000',
                'type' => 'invoice',
                'status' => 'submitted',
            ],
        ]),
    ]);

    $action = app(GetInvoiceStatusAction::class);
    $result = $action->execute('123');

    expect($result->id)->toBe(123)
        ->and($result->status)->toBe('submitted');
});

it('throws invoice exception on 404', function () {
    Http::fake([
        'api.example.com/api/invoices/nonexistent/status' => Http::response([
            'status' => 'not_found',
        ], 404),
    ]);

    $action = app(GetInvoiceStatusAction::class);
    $action->execute('nonexistent');
})->throws(InvoiceException::class, "Invoice with ID 'nonexistent' was not found");

it('throws authentication exception on 401', function () {
    Http::fake([
        'api.example.com/api/invoices/123/status' => Http::response(['message' => 'Unauthorized'], 401),
    ]);

    $action = app(GetInvoiceStatusAction::class);
    $action->execute('123');
})->throws(AuthenticationException::class);

it('throws connection exception on network failure', function () {
    Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('Timeout'));

    $action = app(GetInvoiceStatusAction::class);
    $action->execute('123');
})->throws(ConnectionException::class);

it('handles different invoice statuses', function (string $status) {
    Http::fake([
        'api.example.com/api/invoices/123/status' => Http::response([
            'invoice' => [
                'id' => 123,
                'uuid' => '550e8400-e29b-41d4-a716-446655440000',
                'type' => 'invoice',
                'status' => $status,
            ],
        ]),
    ]);

    $action = app(GetInvoiceStatusAction::class);
    $result = $action->execute('123');

    expect($result->status)->toBe($status);
})->with([
    'draft',
    'validated',
    'submitted',
    'delivered',
    'rejected',
    'failed',
    'expired',
]);

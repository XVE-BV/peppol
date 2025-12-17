<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Xve\LaravelPeppol\Actions\SendCreditNoteAction;
use Xve\LaravelPeppol\Exceptions\AuthenticationException;
use Xve\LaravelPeppol\Exceptions\ValidationException;
use Xve\LaravelPeppol\Support\InvoiceResult;

beforeEach(function () {
    config()->set('peppol-gateway.base_url', 'https://api.example.com');
    config()->set('peppol-gateway.client_id', 'test-client');
    config()->set('peppol-gateway.client_secret', 'test-secret');
});

it('sends credit note successfully', function () {
    Http::fake([
        'api.example.com/api/credit-notes/json' => Http::response([
            'status' => 'queued',
            'uuid' => '550e8400-e29b-41d4-a716-446655440000',
        ], 202),
    ]);

    $action = app(SendCreditNoteAction::class);
    $result = $action->execute([
        'type' => 'credit_note',
        'id' => 'CN-001',
        'total' => -121.00,
        'currency' => 'EUR',
        'buyer_vat' => 'BE0123456789',
    ]);

    expect($result)->toBeInstanceOf(InvoiceResult::class)
        ->and($result->status)->toBe('queued')
        ->and($result->uuid)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('sends credit note data to correct endpoint', function () {
    Http::fake([
        'api.example.com/api/credit-notes/json' => Http::response([
            'status' => 'queued',
            'uuid' => '550e8400-e29b-41d4-a716-446655440000',
        ], 202),
    ]);

    $action = app(SendCreditNoteAction::class);
    $action->execute([
        'type' => 'credit_note',
        'total' => -121.00,
        'currency' => 'EUR',
    ]);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/api/credit-notes/json')
            && $request['type'] === 'credit_note'
            && $request['total'] === -121.00;
    });
});

it('throws authentication exception on 401', function () {
    Http::fake([
        'api.example.com/api/credit-notes/json' => Http::response(['message' => 'Unauthorized'], 401),
    ]);

    $action = app(SendCreditNoteAction::class);
    $action->execute(['type' => 'credit_note', 'total' => -100, 'currency' => 'EUR']);
})->throws(AuthenticationException::class);

it('throws validation exception on 422', function () {
    Http::fake([
        'api.example.com/api/credit-notes/json' => Http::response([
            'message' => 'Validation failed',
            'errors' => ['total' => ['Total must be negative']],
        ], 422),
    ]);

    $action = app(SendCreditNoteAction::class);
    $action->execute(['type' => 'credit_note', 'total' => 100, 'currency' => 'EUR']);
})->throws(ValidationException::class);

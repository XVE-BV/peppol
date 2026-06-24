<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Xve\LaravelPeppol\Actions\LookupParticipantAction;
use Xve\LaravelPeppol\Exceptions\AuthenticationException;
use Xve\LaravelPeppol\Exceptions\ConnectionException;
use Xve\LaravelPeppol\Exceptions\ValidationException;
use Xve\LaravelPeppol\Support\Participant;

beforeEach(function (): void {
    config()->set('peppol-gateway.base_url', 'https://api.example.com');
    config()->set('peppol-gateway.client_id', 'test-client');
    config()->set('peppol-gateway.client_secret', 'test-secret');
});

it('looks up participant successfully', function (): void {
    Http::fake([
        'api.example.com/api/peppol/lookup' => Http::response([
            'data' => [
                'id' => '8ea99b6a-c891-4f48-964e-208b49a19c93',
                'type' => 'peppolCustomerSearch',
                'attributes' => [
                    'customerReference' => '0208:0805374964',
                    'supportedDocumentFormats' => [
                        ['localName' => 'Invoice'],
                    ],
                ],
            ],
        ]),
    ]);

    $action = app(LookupParticipantAction::class);
    $result = $action->execute('BE0123456789');

    expect($result)->toBeInstanceOf(Participant::class)
        ->and($result->participantId)->toBe('0208:0805374964')
        ->and($result->capable)->toBeTrue();
});

it('sends vat in request body', function (): void {
    Http::fake([
        'api.example.com/api/peppol/lookup' => Http::response([
            'data' => [
                'id' => '8ea99b6a-c891-4f48-964e-208b49a19c93',
                'type' => 'peppolCustomerSearch',
                'attributes' => [
                    'customerReference' => '0208:0805374964',
                    'supportedDocumentFormats' => [],
                ],
            ],
        ]),
    ]);

    $action = app(LookupParticipantAction::class);
    $action->execute('BE0123456789');

    Http::assertSent(fn ($request): bool => $request['vat'] === 'BE0123456789');
});

it('includes country when provided', function (): void {
    Http::fake([
        'api.example.com/api/peppol/lookup' => Http::response([
            'data' => [
                'id' => '8ea99b6a-c891-4f48-964e-208b49a19c93',
                'type' => 'peppolCustomerSearch',
                'attributes' => [
                    'customerReference' => '0208:0805374964',
                    'supportedDocumentFormats' => [],
                ],
            ],
        ]),
    ]);

    $action = app(LookupParticipantAction::class);
    $action->execute('0123456789', 'BE');

    Http::assertSent(fn ($request): bool => $request['vat'] === '0123456789'
        && $request['country'] === 'BE');
});

it('includes force refresh when true', function (): void {
    Http::fake([
        'api.example.com/api/peppol/lookup' => Http::response([
            'data' => [
                'id' => '8ea99b6a-c891-4f48-964e-208b49a19c93',
                'type' => 'peppolCustomerSearch',
                'attributes' => [
                    'customerReference' => '0208:0805374964',
                    'supportedDocumentFormats' => [],
                ],
            ],
        ]),
    ]);

    $action = app(LookupParticipantAction::class);
    $action->execute('BE0123456789', forceRefresh: true);

    Http::assertSent(fn ($request): bool => $request['force_refresh'] === true);
});

it('throws authentication exception on 401', function (): void {
    Http::fake([
        'api.example.com/api/peppol/lookup' => Http::response(['message' => 'Unauthorized'], 401),
    ]);

    $action = app(LookupParticipantAction::class);
    $action->execute('BE0123456789');
})->throws(AuthenticationException::class, 'Invalid API credentials');

it('throws validation exception on 422', function (): void {
    Http::fake([
        'api.example.com/api/peppol/lookup' => Http::response([
            'message' => 'Validation failed',
            'errors' => ['vat' => ['Invalid VAT format']],
        ], 422),
    ]);

    $action = app(LookupParticipantAction::class);
    $action->execute('invalid');
})->throws(ValidationException::class);

it('throws connection exception on network failure', function (): void {
    Http::fake(fn () => throw new Illuminate\Http\Client\ConnectionException('Timeout'));

    $action = app(LookupParticipantAction::class);
    $action->execute('BE0123456789');
})->throws(ConnectionException::class);

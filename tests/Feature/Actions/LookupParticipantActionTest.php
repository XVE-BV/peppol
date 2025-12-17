<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Xve\LaravelPeppol\Actions\LookupParticipantAction;
use Xve\LaravelPeppol\Exceptions\AuthenticationException;
use Xve\LaravelPeppol\Exceptions\ConnectionException;
use Xve\LaravelPeppol\Exceptions\ValidationException;
use Xve\LaravelPeppol\Support\Participant;

beforeEach(function () {
    config()->set('peppol-gateway.base_url', 'https://api.example.com');
    config()->set('peppol-gateway.client_id', 'test-client');
    config()->set('peppol-gateway.client_secret', 'test-secret');
});

it('looks up participant successfully', function () {
    Http::fake([
        'api.example.com/api/peppol/lookup' => Http::response([
            'participant_id' => '9925:BE0123456789',
            'vat' => 'BE0123456789',
            'capable' => true,
            'documentTypes' => ['urn:fdc:peppol.eu:2017:poacc:billing:01:1.0'],
        ]),
    ]);

    $action = app(LookupParticipantAction::class);
    $result = $action->execute('BE0123456789');

    expect($result)->toBeInstanceOf(Participant::class)
        ->and($result->participantId)->toBe('9925:BE0123456789')
        ->and($result->capable)->toBeTrue();
});

it('sends vat in request body', function () {
    Http::fake([
        'api.example.com/api/peppol/lookup' => Http::response([
            'participant_id' => '9925:BE0123456789',
            'vat' => 'BE0123456789',
            'capable' => true,
        ]),
    ]);

    $action = app(LookupParticipantAction::class);
    $action->execute('BE0123456789');

    Http::assertSent(function ($request) {
        return $request['vat'] === 'BE0123456789';
    });
});

it('includes country when provided', function () {
    Http::fake([
        'api.example.com/api/peppol/lookup' => Http::response([
            'participant_id' => '9925:BE0123456789',
            'vat' => 'BE0123456789',
            'capable' => true,
        ]),
    ]);

    $action = app(LookupParticipantAction::class);
    $action->execute('0123456789', 'BE');

    Http::assertSent(function ($request) {
        return $request['vat'] === '0123456789'
            && $request['country'] === 'BE';
    });
});

it('includes force refresh when true', function () {
    Http::fake([
        'api.example.com/api/peppol/lookup' => Http::response([
            'participant_id' => '9925:BE0123456789',
            'vat' => 'BE0123456789',
            'capable' => true,
        ]),
    ]);

    $action = app(LookupParticipantAction::class);
    $action->execute('BE0123456789', forceRefresh: true);

    Http::assertSent(function ($request) {
        return $request['force_refresh'] === true;
    });
});

it('throws authentication exception on 401', function () {
    Http::fake([
        'api.example.com/api/peppol/lookup' => Http::response(['message' => 'Unauthorized'], 401),
    ]);

    $action = app(LookupParticipantAction::class);
    $action->execute('BE0123456789');
})->throws(AuthenticationException::class, 'Invalid API credentials');

it('throws validation exception on 422', function () {
    Http::fake([
        'api.example.com/api/peppol/lookup' => Http::response([
            'message' => 'Validation failed',
            'errors' => ['vat' => ['Invalid VAT format']],
        ], 422),
    ]);

    $action = app(LookupParticipantAction::class);
    $action->execute('invalid');
})->throws(ValidationException::class);

it('throws connection exception on network failure', function () {
    Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('Timeout'));

    $action = app(LookupParticipantAction::class);
    $action->execute('BE0123456789');
})->throws(ConnectionException::class);

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Xve\LaravelPeppol\Actions\ValidateVatCombinationAction;
use Xve\LaravelPeppol\Exceptions\AuthenticationException;
use Xve\LaravelPeppol\Exceptions\ConnectionException;
use Xve\LaravelPeppol\Support\VatValidationResult;

beforeEach(function (): void {
    config()->set('peppol-gateway.base_url', 'https://api.example.com');
    config()->set('peppol-gateway.client_id', 'test-client');
    config()->set('peppol-gateway.client_secret', 'test-secret');
});

it('returns accepted result when all combinations are mapped', function (): void {
    Http::fake([
        'api.example.com/api/vat/validate-combination' => Http::response([
            'accepted' => true,
            'missing' => [],
        ], 200),
    ]);

    $action = app(ValidateVatCombinationAction::class);
    $result = $action->execute(['standard|700100|21|']);

    expect($result)->toBeInstanceOf(VatValidationResult::class)
        ->and($result->accepted)->toBeTrue()
        ->and($result->missing)->toBe([]);
});

it('returns rejected result with missing codes when unmapped', function (): void {
    Http::fake([
        'api.example.com/api/vat/validate-combination' => Http::response([
            'accepted' => false,
            'missing' => ['VAT_COMBINATION:standard|700100|21|', 'VAT_COMBINATION:standard|700100|0|'],
        ], 200),
    ]);

    $action = app(ValidateVatCombinationAction::class);
    $result = $action->execute(['standard|700100|21|', 'standard|700100|0|']);

    expect($result->accepted)->toBeFalse()
        ->and($result->missing)->toBe([
            'VAT_COMBINATION:standard|700100|21|',
            'VAT_COMBINATION:standard|700100|0|',
        ]);
});

it('sends combinations in request body', function (): void {
    Http::fake([
        'api.example.com/api/vat/validate-combination' => Http::response([
            'accepted' => true,
            'missing' => [],
        ], 200),
    ]);

    $action = app(ValidateVatCombinationAction::class);
    $action->execute(['standard|700100|21|']);

    Http::assertSent(fn ($request): bool => $request->url() === 'https://api.example.com/api/vat/validate-combination'
        && $request['combinations'] === ['standard|700100|21|']);
});

it('throws authentication exception on 401', function (): void {
    Http::fake([
        'api.example.com/api/vat/validate-combination' => Http::response(['message' => 'Unauthorized'], 401),
    ]);

    $action = app(ValidateVatCombinationAction::class);
    $action->execute(['standard|700100|21|']);
})->throws(AuthenticationException::class);

it('throws connection exception on network failure', function (): void {
    Http::fake([
        'api.example.com/api/vat/validate-combination' => fn () => throw new Illuminate\Http\Client\ConnectionException('Connection refused'),
    ]);

    $action = app(ValidateVatCombinationAction::class);
    $action->execute(['standard|700100|21|']);
})->throws(ConnectionException::class);

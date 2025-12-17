<?php

declare(strict_types=1);

use Xve\LaravelPeppol\Actions\GetInvoiceStatusAction;
use Xve\LaravelPeppol\Actions\HealthCheckAction;
use Xve\LaravelPeppol\Actions\LookupParticipantAction;
use Xve\LaravelPeppol\Actions\SendCreditNoteAction;
use Xve\LaravelPeppol\Actions\SendInvoiceAction;

it('can resolve health check action from container', function () {
    $action = app(HealthCheckAction::class);

    expect($action)->toBeInstanceOf(HealthCheckAction::class);
});

it('can resolve lookup participant action from container', function () {
    $action = app(LookupParticipantAction::class);

    expect($action)->toBeInstanceOf(LookupParticipantAction::class);
});

it('can resolve send invoice action from container', function () {
    $action = app(SendInvoiceAction::class);

    expect($action)->toBeInstanceOf(SendInvoiceAction::class);
});

it('can resolve send credit note action from container', function () {
    $action = app(SendCreditNoteAction::class);

    expect($action)->toBeInstanceOf(SendCreditNoteAction::class);
});

it('can resolve get invoice status action from container', function () {
    $action = app(GetInvoiceStatusAction::class);

    expect($action)->toBeInstanceOf(GetInvoiceStatusAction::class);
});

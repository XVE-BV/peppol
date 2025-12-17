<?php

declare(strict_types=1);

use Xve\LaravelPeppol\Actions\GetInvoiceStatusAction;
use Xve\LaravelPeppol\Actions\HealthCheckAction;
use Xve\LaravelPeppol\Actions\LookupParticipantAction;
use Xve\LaravelPeppol\Actions\SendCreditNoteAction;
use Xve\LaravelPeppol\Actions\SendInvoiceAction;

return [

    /*
    |--------------------------------------------------------------------------
    | API Connection
    |--------------------------------------------------------------------------
    */
    'base_url' => env('PEPPOL_GATEWAY_URL'),
    'timeout' => env('PEPPOL_GATEWAY_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */
    'client_id' => env('PEPPOL_GATEWAY_CLIENT_ID'),
    'client_secret' => env('PEPPOL_GATEWAY_CLIENT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    |
    | You can swap out the default action classes with your own implementations.
    |
    */
    'actions' => [
        'health_check' => HealthCheckAction::class,
        'lookup_participant' => LookupParticipantAction::class,
        'send_invoice' => SendInvoiceAction::class,
        'send_credit_note' => SendCreditNoteAction::class,
        'get_invoice_status' => GetInvoiceStatusAction::class,
    ],

];

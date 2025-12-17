<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Actions;

use Xve\LaravelPeppol\Exceptions\AuthenticationException;
use Xve\LaravelPeppol\Exceptions\ConnectionException;
use Xve\LaravelPeppol\Exceptions\InvoiceException;
use Xve\LaravelPeppol\Exceptions\ValidationException;
use Xve\LaravelPeppol\Support\Config;
use Xve\LaravelPeppol\Support\InvoiceResult;

class SendInvoiceAction
{
    public function execute(array $data): InvoiceResult
    {
        try {
            $response = Config::httpClient()->post('/api/invoices/json', $data);

            if ($response->status() === 401) {
                throw AuthenticationException::invalidCredentials();
            }

            if ($response->status() === 422) {
                throw ValidationException::fromResponse($response->json('errors', []));
            }

            if ($response->failed()) {
                throw InvoiceException::sendFailed($response->json('message', 'Unknown error'));
            }

            return InvoiceResult::fromResponse($response->json());
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw ConnectionException::unreachable();
        }
    }
}

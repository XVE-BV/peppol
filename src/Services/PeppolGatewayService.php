<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Xve\LaravelPeppol\Exceptions\AuthenticationException;
use Xve\LaravelPeppol\Exceptions\ConnectionException;
use Xve\LaravelPeppol\Exceptions\InvoiceException;
use Xve\LaravelPeppol\Exceptions\ValidationException;

class PeppolGatewayService
{
    public function __construct(
        protected string $baseUrl,
        protected string $clientId,
        protected string $clientSecret,
        protected int $timeout = 30,
    ) {}

    public function healthCheck(): array
    {
        try {
            $response = $this->client()->get('/api/system/health');

            if ($response->status() === 401) {
                throw AuthenticationException::invalidCredentials();
            }

            // Health check returns valid responses even on 5xx (unhealthy status)
            return $response->json() ?? [];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw ConnectionException::unreachable();
        }
    }

    public function lookupParticipant(string $vat, ?string $country = null, bool $forceRefresh = false): array
    {
        // Normalize VAT by removing dots, spaces, and dashes
        $normalizedVat = preg_replace('/[\s.\-]/', '', $vat);

        $payload = ['vat' => $normalizedVat];

        if ($country !== null) {
            $payload['country'] = $country;
        }

        if ($forceRefresh) {
            $payload['force_refresh'] = true;
        }

        return $this->post('/api/peppol/lookup', $payload);
    }

    public function sendInvoice(array $data): array
    {
        return $this->post('/api/invoices/json', $data);
    }

    public function sendCreditNote(array $data): array
    {
        return $this->post('/api/credit-notes/json', $data);
    }

    public function getInvoice(string $id): array
    {
        return $this->get("/api/invoices/{$id}");
    }

    protected function get(string $endpoint): array
    {
        try {
            $response = $this->client()->get($endpoint);

            return $this->handleResponse($response);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw ConnectionException::unreachable();
        }
    }

    protected function post(string $endpoint, array $data = []): array
    {
        try {
            $response = $this->client()->post($endpoint, $data);

            return $this->handleResponse($response);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw ConnectionException::unreachable();
        }
    }

    protected function handleResponse(Response $response): array
    {
        if ($response->status() === 401) {
            throw AuthenticationException::invalidCredentials();
        }

        if ($response->status() === 422) {
            throw ValidationException::fromResponse($response->json('errors', []));
        }

        if ($response->status() === 404) {
            return ['_status' => 404, ...$response->json()];
        }

        if ($response->failed()) {
            $message = $response->json('message') ?? $response->json('error') ?? 'Unknown error';
            throw InvoiceException::sendFailed(is_string($message) ? $message : json_encode($message));
        }

        return $response->json() ?? [];
    }

    protected function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->withHeaders([
                'X-Api-Client-Id' => $this->clientId,
                'Authorization' => 'Bearer '.$this->clientSecret,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]);
    }
}

<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Actions;

use Xve\LaravelPeppol\Exceptions\AuthenticationException;
use Xve\LaravelPeppol\Exceptions\ConnectionException;
use Xve\LaravelPeppol\Exceptions\ValidationException;
use Xve\LaravelPeppol\Support\Config;
use Xve\LaravelPeppol\Support\Participant;

class LookupParticipantAction
{
    public function execute(
        string $vat,
        ?string $country = null,
        bool $forceRefresh = false,
    ): Participant {
        try {
            $payload = ['vat' => $vat];

            if ($country !== null) {
                $payload['country'] = $country;
            }

            if ($forceRefresh) {
                $payload['force_refresh'] = true;
            }

            $response = Config::httpClient()->post('/api/peppol/lookup', $payload);

            if ($response->status() === 401) {
                throw AuthenticationException::invalidCredentials();
            }

            if ($response->status() === 422) {
                throw ValidationException::fromResponse($response->json('errors', []));
            }

            return Participant::fromResponse($response->json());
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw ConnectionException::unreachable();
        }
    }
}

<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Actions;

use Xve\LaravelPeppol\Exceptions\ConnectionException;
use Xve\LaravelPeppol\Support\Config;
use Xve\LaravelPeppol\Support\HealthStatus;

class HealthCheckAction
{
    public function execute(): HealthStatus
    {
        try {
            $response = Config::httpClient()->get('/api/system/health');

            return HealthStatus::fromResponse($response->json());
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw ConnectionException::unreachable();
        }
    }
}

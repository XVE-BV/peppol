<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Actions;

use Xve\LaravelPeppol\Events\HealthChecked;
use Xve\LaravelPeppol\Services\PeppolGatewayService;
use Xve\LaravelPeppol\Support\HealthStatus;

class HealthCheckAction
{
    public function __construct(
        protected PeppolGatewayService $service,
    ) {}

    public function execute(): HealthStatus
    {
        $response = $this->service->healthCheck();

        $status = HealthStatus::fromResponse($response);

        HealthChecked::dispatch($status);

        return $status;
    }
}

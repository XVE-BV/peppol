<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Actions;

use Xve\LaravelPeppol\Events\InvoiceStatusRetrieved;
use Xve\LaravelPeppol\Exceptions\InvoiceException;
use Xve\LaravelPeppol\Services\PeppolGatewayService;
use Xve\LaravelPeppol\Support\InvoiceStatus;

class GetInvoiceStatusAction
{
    public function __construct(
        protected PeppolGatewayService $service,
    ) {}

    public function execute(string $id): InvoiceStatus
    {
        $response = $this->service->getInvoice($id);

        if (($response['_status'] ?? null) === 404) {
            throw InvoiceException::notFound($id);
        }

        $status = InvoiceStatus::fromResponse($response);

        InvoiceStatusRetrieved::dispatch($id, $status);

        return $status;
    }
}

<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Actions;

use Xve\LaravelPeppol\Events\InvoiceSent;
use Xve\LaravelPeppol\Services\PeppolGatewayService;
use Xve\LaravelPeppol\Support\InvoiceResult;

class SendInvoiceAction
{
    public function __construct(
        protected PeppolGatewayService $service,
    ) {}

    public function execute(array $data): InvoiceResult
    {
        $response = $this->service->sendInvoice($data);

        $result = InvoiceResult::fromResponse($response);

        InvoiceSent::dispatch($data, $result);

        return $result;
    }
}

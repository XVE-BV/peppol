<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Actions;

use Xve\LaravelPeppol\Events\CreditNoteSent;
use Xve\LaravelPeppol\Services\PeppolGatewayService;
use Xve\LaravelPeppol\Support\InvoiceResult;

class SendCreditNoteAction
{
    public function __construct(
        protected PeppolGatewayService $service,
    ) {}

    public function execute(array $data): InvoiceResult
    {
        $response = $this->service->sendCreditNote($data);

        $result = InvoiceResult::fromResponse($response);

        CreditNoteSent::dispatch($data, $result);

        return $result;
    }
}

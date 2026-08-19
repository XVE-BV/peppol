<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Actions;

use Xve\LaravelPeppol\Services\PeppolGatewayService;
use Xve\LaravelPeppol\Support\VatValidationResult;

class ValidateVatCombinationAction
{
    public function __construct(
        protected PeppolGatewayService $service,
    ) {}

    /**
     * @param  list<string>  $combinations  e.g. ['standard|700100|21|', 'excemption|700100|0|']
     */
    public function execute(array $combinations): VatValidationResult
    {
        $response = $this->service->validateVatCombination($combinations);

        return VatValidationResult::fromResponse($response);
    }
}

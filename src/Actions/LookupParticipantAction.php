<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Actions;

use Xve\LaravelPeppol\Events\ParticipantLookedUp;
use Xve\LaravelPeppol\Services\PeppolGatewayService;
use Xve\LaravelPeppol\Support\Participant;

class LookupParticipantAction
{
    public function __construct(
        protected PeppolGatewayService $service,
    ) {}

    public function execute(
        string $vat,
        ?string $country = null,
        bool $forceRefresh = false,
    ): Participant {
        $response = $this->service->lookupParticipant($vat, $country, $forceRefresh);

        $participant = Participant::fromResponse($response);

        ParticipantLookedUp::dispatch($vat, $participant);

        return $participant;
    }
}

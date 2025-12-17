<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Models\Concerns;

interface HasPeppolId
{
    public function getPeppolParticipantId(): ?string;

    public function setPeppolParticipantId(string $id): void;
}

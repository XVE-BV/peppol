<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Models\Concerns;

trait InteractsWithPeppol
{
    public function getPeppolParticipantId(): ?string
    {
        return $this->peppol_id;
    }

    public function setPeppolParticipantId(string $id): void
    {
        $this->peppol_id = $id;
        $this->save();
    }
}

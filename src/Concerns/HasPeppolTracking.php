<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @mixin Model
 *
 * @property string|null $peppol_uuid
 * @property string|null $peppol_status
 * @property Carbon|null $peppol_sent_at
 * @property Carbon|null $peppol_status_updated_at
 * @property string|null $peppol_error
 */
trait HasPeppolTracking
{
    public function initializeHasPeppolTracking(): void
    {
        $this->mergeCasts([
            'peppol_sent_at' => 'datetime',
            'peppol_status_updated_at' => 'datetime',
        ]);
    }

    /**
     * Check if this document was already sent to Peppol.
     */
    public function wasSentToPeppol(): bool
    {
        return $this->peppol_uuid !== null;
    }

    /**
     * Get the Peppol UUID assigned after sending.
     */
    public function getPeppolUuid(): ?string
    {
        return $this->peppol_uuid;
    }

    /**
     * Get the current Peppol status.
     */
    public function getPeppolStatus(): ?string
    {
        return $this->peppol_status;
    }

    /**
     * Check if Peppol delivery is pending.
     */
    public function isPeppolPending(): bool
    {
        return $this->wasSentToPeppol()
            && ! in_array($this->peppol_status, ['delivered', 'failed', 'rejected'], true);
    }

    /**
     * Check if Peppol delivery was successful.
     */
    public function isPeppolDelivered(): bool
    {
        return $this->peppol_status === 'delivered';
    }

    /**
     * Check if Peppol delivery failed.
     */
    public function isPeppolFailed(): bool
    {
        return in_array($this->peppol_status, ['failed', 'rejected'], true);
    }

    /**
     * Scope to query documents that have been sent to Peppol.
     */
    public function scopePeppolSent(Builder $query): Builder
    {
        return $query->whereNotNull('peppol_uuid');
    }

    /**
     * Scope to query documents with pending Peppol delivery.
     */
    public function scopePeppolPending(Builder $query): Builder
    {
        return $query->whereNotNull('peppol_uuid')
            ->whereNotIn('peppol_status', ['delivered', 'failed', 'rejected']);
    }

    /**
     * Scope to query documents with failed Peppol delivery.
     */
    public function scopePeppolFailed(Builder $query): Builder
    {
        return $query->whereIn('peppol_status', ['failed', 'rejected']);
    }

    /**
     * Scope to query documents that were successfully delivered via Peppol.
     */
    public function scopePeppolDelivered(Builder $query): Builder
    {
        return $query->where('peppol_status', 'delivered');
    }
}

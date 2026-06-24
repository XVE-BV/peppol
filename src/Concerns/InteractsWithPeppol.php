<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @mixin Model
 *
 * @property string|null $peppol_id
 * @property string|null $peppol_custom_set_id
 * @property Carbon|null $peppol_fetched_at
 * @property bool $use_peppol
 */
trait InteractsWithPeppol
{
    public function initializeInteractsWithPeppol(): void
    {
        $this->mergeCasts([
            'peppol_fetched_at' => 'datetime',
            'use_peppol' => 'boolean',
        ]);
    }

    /**
     * Get the effective Peppol ID (custom takes precedence over fetched).
     */
    public function getPeppolId(): ?string
    {
        return $this->peppol_custom_set_id ?? $this->peppol_id;
    }

    /**
     * Check if this model has a Peppol ID (custom or fetched).
     */
    public function hasPeppolId(): bool
    {
        return $this->getPeppolId() !== null;
    }

    /**
     * Check if Peppol is enabled for this model.
     */
    public function usesPeppol(): bool
    {
        return $this->use_peppol;
    }

    /**
     * Check if a custom Peppol ID is set.
     */
    public function hasCustomPeppolId(): bool
    {
        return $this->peppol_custom_set_id !== null;
    }

    /**
     * Scope to query models that use Peppol.
     */
    public function scopeUsesPeppol(Builder $query): Builder
    {
        return $query->where('use_peppol', true);
    }

    /**
     * Scope to query models that have a Peppol ID.
     */
    public function scopeHasPeppolId(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            $q->whereNotNull('peppol_id')
                ->orWhereNotNull('peppol_custom_set_id');
        });
    }
}

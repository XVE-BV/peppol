<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Xve\LaravelPeppol\Actions\GetInvoiceStatusAction;
use Xve\LaravelPeppol\Actions\HealthCheckAction;
use Xve\LaravelPeppol\Actions\LookupParticipantAction;
use Xve\LaravelPeppol\Actions\SendCreditNoteAction;
use Xve\LaravelPeppol\Actions\SendInvoiceAction;

class LaravelPeppolServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('peppol-gateway')
            ->hasConfigFile('peppol-gateway');
    }

    public function packageRegistered(): void
    {
        $this->app->bind(HealthCheckAction::class, fn () => new HealthCheckAction);
        $this->app->bind(LookupParticipantAction::class, fn () => new LookupParticipantAction);
        $this->app->bind(SendInvoiceAction::class, fn () => new SendInvoiceAction);
        $this->app->bind(SendCreditNoteAction::class, fn () => new SendCreditNoteAction);
        $this->app->bind(GetInvoiceStatusAction::class, fn () => new GetInvoiceStatusAction);
    }
}

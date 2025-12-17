<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Xve\LaravelPeppol\Commands\PeppolCommand;

class LaravelPeppolServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-peppol')
            ->hasConfigFile('peppol')
            ->hasCommand(PeppolCommand::class);
    }
}

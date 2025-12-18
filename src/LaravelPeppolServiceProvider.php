<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Xve\LaravelPeppol\Services\PeppolGatewayService;
use Xve\LaravelPeppol\Support\Config;

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
        $this->app->singleton(PeppolGatewayService::class, function () {
            return new PeppolGatewayService(
                baseUrl: Config::getBaseUrl(),
                clientId: Config::getClientId(),
                clientSecret: Config::getClientSecret(),
                timeout: Config::getTimeout(),
            );
        });
    }

    public function packageBooted(): void
    {
        $stubsPath = dirname(__DIR__).'/stubs';

        $this->publishes([
            $stubsPath => base_path('stubs/peppol-gateway'),
        ], 'peppol-gateway-stubs');
    }
}

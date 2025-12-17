<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Xve\LaravelPeppol\LaravelPeppolServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelPeppolServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('peppol-gateway.base_url', 'https://test-gateway.example.com');
        $app['config']->set('peppol-gateway.client_id', 'test-client-id');
        $app['config']->set('peppol-gateway.client_secret', 'test-client-secret');
    }
}

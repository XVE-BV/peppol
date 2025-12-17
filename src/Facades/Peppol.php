<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Xve\LaravelPeppol\LaravelPeppol
 */
class Peppol extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Xve\LaravelPeppol\LaravelPeppol::class;
    }
}

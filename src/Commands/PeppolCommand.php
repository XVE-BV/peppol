<?php

declare(strict_types=1);

namespace Xve\LaravelPeppol\Commands;

use Illuminate\Console\Command;

class PeppolCommand extends Command
{
    public $signature = 'peppol';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

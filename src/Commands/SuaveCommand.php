<?php

namespace Codelabmw\Suave\Commands;

use Illuminate\Console\Command;

class SuaveCommand extends Command
{
    public $signature = 'suave';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

<?php

namespace Codelabmw\Suave\Commands;

use Codelabmw\Suave\Traits\Installer;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\warning;

class SuaveCommand extends Command
{
    use Installer;

    /**
     * The name and signature of the console command.
     */
    public $signature = 'suave:install {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    /**
     * The console command description.
     */
    public $description = 'Installs all necessary API authentication files.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // TODO: mention file names that might be overwritten.
        warning('Please note that this will overwrite similar named files.');

        $confirmation = confirm('Do you want to continue?');

        if (!$confirmation) {
            info('Installation aborted.');

            return self::SUCCESS;
        }

        // Install Sanctum & copy files.
        $this->installApi();

        info('Installation successful!');

        return self::SUCCESS;
    }
}

<?php

namespace Codelabmw\Suave\Commands;

use Codelabmw\Suave\Services\InstallationService;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\warning;

class SuaveCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    public $signature = 'suave:install';

    /**
     * The console command description.
     */
    public $description = 'Installs all necessary API authentication files.';

    /**
     * Create a new command instance.
     */
    public function __construct(private InstallationService $installationService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $options = multiselect(
            label: 'What would you like to install?',
            options: ['Tokens', 'Sessions'],
            hint: 'Use space to select options.'
        );

        if (count($options) === 0) {
            info('No option selected. Exiting...');

            return self::SUCCESS;
        }

        warning('Please note that this will overwrite similar named files.');

        $confirmation = confirm('Do you want to continue?');

        if (! $confirmation) {
            info('Installation aborted.');

            return self::SUCCESS;
        }

        // TODO: Check if user already installed sanctum, if not run install:api artisan command to install sanctum & generate default files.

        foreach ($options as $option) {
            $installer = $this->installationService->getInstaller(str($option)->lower());

            spin(message: "Installing $option...", callback: function () use ($installer) {
                $installer->install();
            });
        }

        info('Installation successful!');

        return self::SUCCESS;
    }
}

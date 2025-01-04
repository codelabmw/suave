<?php

namespace Codelabmw\Suave;

use Codelabmw\Suave\Commands\SuaveCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SuaveServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('suave')
            ->hasConfigFile()
            ->hasMigrations(['create_verification_codes_table', 'add_must_reset_password_to_users_table'])
            ->hasCommand(SuaveCommand::class);
    }
}

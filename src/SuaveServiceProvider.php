<?php

namespace Codelabmw\Suave;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Codelabmw\Suave\Commands\SuaveCommand;

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
            ->hasViews()
            ->hasMigration('create_suave_table')
            ->hasCommand(SuaveCommand::class);
    }
}

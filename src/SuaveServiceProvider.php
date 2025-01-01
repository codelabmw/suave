<?php

namespace Codelabmw\Suave;

use Codelabmw\Suave\Commands\SuaveCommand;
use Codelabmw\Suave\Installers\SessionsInstaller;
use Codelabmw\Suave\Installers\TokensInstaller;
use Codelabmw\Suave\Services\InstallationService;
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
            ->hasCommand(SuaveCommand::class);
    }

    public function packageRegistered()
    {
        $this->app->bind(InstallationService::class, function () {
            return new InstallationService([
                'tokens' => TokensInstaller::class,
                'sessions' => SessionsInstaller::class,
            ]);
        });
    }
}

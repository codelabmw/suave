<?php

use Codelabmw\Suave\Contracts\Installer;
use Codelabmw\Suave\Exceptions\InstallerNotFoundException;
use Codelabmw\Suave\Services\InstallationService;

it('can get a specific installer', function () {
    $installationService = new InstallationService([
        'tokens' => new class implements Installer
        {
            public function install(): void
            {
                // Install tokens
            }
        },
    ]);

    $installer = $installationService->getInstaller('tokens');

    expect($installer)->toBeInstanceOf(Installer::class);
});

it('throws an exception when installer is not found', function () {
    $installationService = new InstallationService;
    $installationService->getInstaller('sessions');
})->throws(InstallerNotFoundException::class);

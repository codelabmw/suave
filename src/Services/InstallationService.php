<?php

declare(strict_types=1);

namespace Codelabmw\Suave\Services;

use Codelabmw\Suave\Contracts\Installer;
use Codelabmw\Suave\Exceptions\InstallerNotFoundException;

/**
 * @internal
 */
class InstallationService
{
    /** @var array<string, class-string> */
    protected $installers;

    /**
     * Create a new InstallationService instance.
     *
     * @param  array<string, class-string>  $installers
     */
    public function __construct($installers = [])
    {
        $this->installers = $installers;
    }

    /**
     * Get a specific registered installer.
     *
     * @throws InstallerNotFoundException
     */
    public function getInstaller(string $name): Installer
    {
        try {
            return new $this->installers[$name];
        } catch (\Exception $e) {
            throw new InstallerNotFoundException("Installer {$name} not found.");
        }
    }
}

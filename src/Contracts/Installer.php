<?php

declare(strict_types=1);

namespace Codelabmw\Suave\Contracts;

/**
 * @internal
 */
interface Installer
{
    /**
     * Install necessary files.
     */
    public function install(): void;
}

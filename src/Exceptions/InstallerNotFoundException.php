<?php

declare(strict_types=1);

namespace Codelabmw\Suave\Exceptions;

use Exception;

class InstallerNotFoundException extends Exception
{
    public function __construct(string $name)
    {
        parent::__construct("Installer with name {$name} not found.");
    }
}

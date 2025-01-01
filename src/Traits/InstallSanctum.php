<?php

namespace Codelabmw\Suave\Traits;

use Illuminate\Support\Arr;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

use RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

trait InstallSanctum
{
    /**
     * Install the API Breeze stack.
     */
    protected function installApi(): ?int
    {
        $this->runCommands(['php artisan install:api']);

        $files = new Filesystem;

        // Middleware...
        $files->copyDirectory(__DIR__ . '/../../stubs/app/Http/Middleware', app_path('Http/Middleware'));

        $this->installMiddlewareAliases([
            'verified' => '\App\Http\Middleware\EnsureEmailIsVerified::class',
        ]);

        $this->installMiddleware([
            '\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class',
        ], 'api', 'prepend');

        // Routes...
        copy(__DIR__ . '/../../stubs/routes/api.php', base_path('routes/api.php'));

        // Configuration...
        $files->copyDirectory(__DIR__ . '/../../stubs/config', config_path());

        // Environment...
        if (!$files->exists(base_path('.env'))) {
            copy(base_path('.env.example'), base_path('.env'));
        }

        file_put_contents(
            base_path('.env'),
            preg_replace('/APP_URL=(.*)/', 'APP_URL=http://localhost:8000' . PHP_EOL . 'FRONTEND_URL=http://localhost:3000', file_get_contents(base_path('.env')))
        );

        // Tests...
        if (!$this->installTests()) {
            return 1;
        }
    }

    /**
     * Install tests.
     */
    protected function installTests(): bool
    {
        (new Filesystem)->ensureDirectoryExists(base_path('tests/Feature'));

        if ($this->isUsingPest()) {
            if ($this->hasComposerPackage('phpunit/phpunit')) {
                $this->removeComposerPackages(['phpunit/phpunit'], true);
            }

            if (!$this->requireComposerPackages(['pestphp/pest', 'pestphp/pest-plugin-laravel'], true)) {
                return false;
            }

            (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/pest-tests/Feature', base_path('tests/Feature'));
            (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/pest-tests/Unit', base_path('tests/Unit'));
            (new Filesystem)->copy(__DIR__ . '/../../stubs/pest-tests/Pest.php', base_path('tests/Pest.php'));
        } else {
            (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/tests/Feature', base_path('tests/Feature'));
        }

        return true;
    }

    /**
     * Install the given middleware names into the application.
     */
    protected function installMiddleware(array|string $names, string $group = 'web', string $modifier = 'append'): void
    {
        $bootstrapApp = file_get_contents(base_path('bootstrap/app.php'));

        $names = collect(Arr::wrap($names))
            ->filter(fn($name) => !Str::contains($bootstrapApp, $name))
            ->whenNotEmpty(function ($names) use ($bootstrapApp, $group, $modifier) {
                $names = $names->map(fn($name) => "$name")->implode(',' . PHP_EOL . '            ');

                $bootstrapApp = str_replace(
                    '->withMiddleware(function (Middleware $middleware) {',
                    '->withMiddleware(function (Middleware $middleware) {'
                    . PHP_EOL . "        \$middleware->$group($modifier: ["
                    . PHP_EOL . "            $names,"
                    . PHP_EOL . '        ]);'
                    . PHP_EOL,
                    $bootstrapApp,
                );

                file_put_contents(base_path('bootstrap/app.php'), $bootstrapApp);
            });
    }

    /**
     * Install the given middleware aliases into the application.
     */
    protected function installMiddlewareAliases(array $aliases): void
    {
        $bootstrapApp = file_get_contents(base_path('bootstrap/app.php'));

        $aliases = collect($aliases)
            ->filter(fn($alias) => !Str::contains($bootstrapApp, $alias))
            ->whenNotEmpty(function ($aliases) use ($bootstrapApp) {
                $aliases = $aliases->map(fn($name, $alias) => "'$alias' => $name")->implode(',' . PHP_EOL . '            ');

                $bootstrapApp = str_replace(
                    '->withMiddleware(function (Middleware $middleware) {',
                    '->withMiddleware(function (Middleware $middleware) {'
                    . PHP_EOL . '        $middleware->alias(['
                    . PHP_EOL . "            $aliases,"
                    . PHP_EOL . '        ]);'
                    . PHP_EOL,
                    $bootstrapApp,
                );

                file_put_contents(base_path('bootstrap/app.php'), $bootstrapApp);
            });
    }

    /**
     * Determine if the given Composer package is installed.
     */
    protected function hasComposerPackage(string $package): bool
    {
        $packages = json_decode(file_get_contents(base_path('composer.json')), true);

        return array_key_exists($package, $packages['require'] ?? [])
            || array_key_exists($package, $packages['require-dev'] ?? []);
    }

    /**
     * Installs the given Composer Packages into the application.
     */
    protected function requireComposerPackages(array $packages, bool $asDev = false): bool
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            $packages,
            $asDev ? ['--dev'] : [],
        );

        return (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            }) === 0;
    }

    /**
     * Removes the given Composer Packages from the application.
     */
    protected function removeComposerPackages(array $packages, bool $asDev = false): bool
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'remove'];
        }

        $command = array_merge(
            $command ?? ['composer', 'remove'],
            $packages,
            $asDev ? ['--dev'] : [],
        );

        return (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            }) === 0;
    }

    /**
     * Replace a given string within a given file.
     */
    protected function replaceInFile(string $search, string $replace, string $path): void
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    /**
     * Get the path to the appropriate PHP binary.
     */
    protected function phpBinary(): string
    {
        if (function_exists('Illuminate\Support\php_binary')) {
            return \Illuminate\Support\php_binary();
        }

        return (new PhpExecutableFinder)->find(false) ?: 'php';
    }

    /**
     * Run the given commands.
     */
    protected function runCommands(array $commands): void
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('  <bg=yellow;fg=black> WARN </> ' . $e->getMessage() . PHP_EOL);
            }
        }

        $process->run(function ($type, $line) {
            $this->output->write('    ' . $line);
        });
    }

    /**
     * Determine whether the project is already using Pest.
     */
    protected function isUsingPest(): bool
    {
        return class_exists(\Pest\TestSuite::class);
    }
}

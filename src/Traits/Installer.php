<?php

namespace Codelabmw\Suave\Traits;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

trait Installer
{
    /**
     * Install Sanctum and copy necessary files.
     */
    protected function installApi()
    {
        // Publish suave migrations and configuration file.
        $this->runCommands(['php artisan vendor:publish --tag=suave-config', 'php artisan vendor:publish --tag=suave-migrations']);

        // Install the Sanctum.
        $this->runCommands(['php artisan install:api']);

        $files = new Filesystem;

        // Add api prefix to the application.
        $this->addApiPrefix('v1');

        // Middleware...
        $files->copyDirectory(__DIR__.'/../../stubs/app/Http/Middleware', app_path('Http/Middleware'));

        $this->installMiddlewareAliases([
            'verified' => '\App\Http\Middleware\EnsureEmailIsVerified::class',
            'password.reset' => '\App\Http\Middleware\EnsureUserResetsPassword::class',
        ]);

        $this->installMiddleware([
            '\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class',
        ], 'api', 'prepend');

        // Contracts
        $files->ensureDirectoryExists(app_path('Contracts'));
        $files->copyDirectory(__DIR__.'/../../stubs/app/Contracts', app_path('Contracts'));

        // Events
        $files->ensureDirectoryExists(app_path('Events'));
        $files->copyDirectory(__DIR__.'/../../stubs/app/Events', app_path('Events'));

        // Listeners
        $files->ensureDirectoryExists(app_path('Listeners'));
        $files->copyDirectory(__DIR__.'/../../stubs/app/Listeners', app_path('Listeners'));

        // Models
        $files->ensureDirectoryExists(app_path('Models'));
        $files->copyDirectory(__DIR__.'/../../stubs/app/Models', app_path('Models'));

        // Notifications
        $files->ensureDirectoryExists(app_path('Notifications'));
        $files->copyDirectory(__DIR__.'/../../stubs/app/Notifications', app_path('Notifications'));

        // Traits
        $files->ensureDirectoryExists(app_path('Traits'));
        $files->copyDirectory(__DIR__.'/../../stubs/app/Traits', app_path('Traits'));

        // Controllers...
        $files->ensureDirectoryExists(app_path('Http/Controllers/Account'));
        $files->ensureDirectoryExists(app_path('Http/Controllers/Account/Token'));
        $files->ensureDirectoryExists(app_path('Http/Controllers/Account/Session'));
        $files->copyDirectory(__DIR__.'/../../stubs/app/Http/Controllers/Account', app_path('Http/Controllers/Account'));

        // Configuration...
        $files->copyDirectory(__DIR__.'/../../stubs/config', config_path());

        // Factories...
        $files->copyDirectory(__DIR__.'/../../stubs/database/factories', base_path('database/factories'));

        // Environment...
        if (! $files->exists(base_path('.env'))) {
            copy(base_path('.env.example'), base_path('.env'));
        }

        file_put_contents(
            base_path('.env'),
            preg_replace('/APP_URL=(.*)/', 'APP_URL=http://localhost:8000'.PHP_EOL.'FRONTEND_URL=http://localhost:3000', file_get_contents(base_path('.env')))
        );

        // Routes...
        $files->copyDirectory(__DIR__.'/../../stubs/routes', base_path('routes'));

        // Pest Tests...
        $files->ensureDirectoryExists(base_path('tests/Feature'));
        $files->copyDirectory(__DIR__.'/../../stubs/pest-tests/Feature', base_path('tests/Feature'));
        $files->copyDirectory(__DIR__.'/../../stubs/pest-tests/Unit', base_path('tests/Unit'));
        $files->copy(__DIR__.'/../../stubs/pest-tests/Pest.php', base_path('tests/Pest.php'));
    }

    /**
     * Add apiPrefix argument to withRouting method in the application.
     */
    protected function addApiPrefix(string $prefix): void
    {
        $bootstrapApp = file_get_contents(base_path('bootstrap/app.php'));

        if (Str::contains($bootstrapApp, 'apiPrefix')) {
            return;
        }

        $bootstrapApp = str_replace(
            '->withRouting(',
            '->withRouting('
            .PHP_EOL."        apiPrefix: '$prefix',",
            $bootstrapApp,
        );

        file_put_contents(base_path('bootstrap/app.php'), $bootstrapApp);
    }

    /**
     * Install the given middleware names into the application.
     */
    protected function installMiddleware(array|string $names, string $group = 'web', string $modifier = 'append'): void
    {
        $bootstrapApp = file_get_contents(base_path('bootstrap/app.php'));

        $names = collect(Arr::wrap($names))
            ->filter(fn ($name) => ! Str::contains($bootstrapApp, $name))
            ->whenNotEmpty(function ($names) use ($bootstrapApp, $group, $modifier) {
                $names = $names->map(fn ($name) => "$name")->implode(','.PHP_EOL.'            ');

                $bootstrapApp = str_replace(
                    '->withMiddleware(function (Middleware $middleware) {',
                    '->withMiddleware(function (Middleware $middleware) {'
                    .PHP_EOL."        \$middleware->$group($modifier: ["
                    .PHP_EOL."            $names,"
                    .PHP_EOL.'        ]);'
                    .PHP_EOL,
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
            ->filter(fn ($alias) => ! Str::contains($bootstrapApp, $alias))
            ->whenNotEmpty(function ($aliases) use ($bootstrapApp) {
                $aliases = $aliases->map(fn ($name, $alias) => "'$alias' => $name")->implode(','.PHP_EOL.'            ');

                $bootstrapApp = str_replace(
                    '->withMiddleware(function (Middleware $middleware) {',
                    '->withMiddleware(function (Middleware $middleware) {'
                    .PHP_EOL.'        $middleware->alias(['
                    .PHP_EOL."            $aliases,"
                    .PHP_EOL.'        ]);'
                    .PHP_EOL,
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
                $this->output->writeln('  <bg=yellow;fg=black> WARN </> '.$e->getMessage().PHP_EOL);
            }
        }

        $process->run(function ($type, $line) {
            $this->output->write('    '.$line);
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

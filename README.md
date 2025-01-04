<p align="center">
    <img src="art/suave-banner.png" alt="Suave">
    <p align="center">
        <a href="https://github.com/codelabmw/suave/actions"><img alt="GitHub Workflow Status (master)" src="https://github.com/codelabmw/suave/actions/workflows/run-tests.yml/badge.svg"></a>
        <a href="https://packagist.org/packages/codelabmw/suave"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/codelabmw/suave"></a>
        <a href="https://packagist.org/packages/codelabmw/suave"><img alt="Latest Version" src="https://img.shields.io/packagist/v/codelabmw/suave"></a>
        <a href="https://packagist.org/packages/codelabmw/suave"><img alt="License" src="https://img.shields.io/packagist/l/codelabmw/suave"></a>
    </p>
</p>

A Laravel package that scaffolds API authentication for both token & session based authentication using `laravel/sanctum`. It uses verification codes for email verification and temporary passwords for forgot password instead of redirecting users to frontend.

## Installation

> Requires PHP ^8.3

You can install the package via composer:

```bash
composer require codelabmw/suave --dev
```

## Usage

After package installation, run the following artisan command to scaffold API.

```bash
php artisan suave:install
```

This will install [Sanctum](https://laravel.com/docs/11.x/sanctum) and copy necessary files for api routing. You are allowed to edit any of these files to customize the behavior suitable for your application however the default state is adequate for most applications. Copied files includes:

- [x] Routes
- [x] Contracts
- [x] Controllers
- [x] Middlewares
- [x] Models
- [x] Notifications
- [x] Events
- [x] Listeners
- [x] Traits
- [x] Tests

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Chikondi Kamwendo](https://github.com/kondi3)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

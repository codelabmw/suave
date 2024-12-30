<p align="center">
    <img src="https://raw.githubusercontent.com/codelabmw/suave/main/art/suave-banner.png" height="300" alt="Suave">
    <p align="center">
        <a href="https://github.com/codelabmw/suave/actions"><img alt="GitHub Workflow Status (master)" src="https://github.com/codelabmw/suave/actions/workflows/tests.yml/badge.svg"></a>
        <a href="https://packagist.org/packages/codelabmw/suave"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/codelabmw/suave"></a>
        <a href="https://packagist.org/packages/codelabmw/suave"><img alt="Latest Version" src="https://img.shields.io/packagist/v/codelabmw/suave"></a>
        <a href="https://packagist.org/packages/codelabmw/suave"><img alt="License" src="https://img.shields.io/packagist/l/codelabmw/suave"></a>
    </p>
</p>

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require codelabmw/suave
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="suave-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="suave-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$suave = new Codelabmw\Suave();
echo $suave->echoPhrase('Hello, Codelabmw!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Chikondi Kamwendo](https://github.com/kondi3)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

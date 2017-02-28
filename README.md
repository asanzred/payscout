# asanzred/payscout

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

## Install

Via Composer

``` bash
$ composer require asanzred/payscout
```

Add ServiceProvider in your `app.php` config file.

```php
// config/app.php
'providers' => [
    ...
    Asanzred\Payscout\PayscoutServiceProvider::class,
]
```

and instead on aliases

```php
// config/app.php
'aliases' => [
    ...
    'Payscout'           => Asanzred\Payscout\Facade::class,
]
```

## Configuration

Publish the config by running:

``` bash
    php artisan vendor:publish --provider="Asanzred\Payscout\PayscoutServiceProvider"
```

## Usage

You can find an PayscoutController.php and routes.php with test routes and calls


## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email asanzred@gmail.com instead of using the issue tracker.

## Credits

- [Alberto Sanz Redondo][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/asanzred/payscout.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/asanzred/payscout.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/asanzred/payscout
[link-downloads]: https://packagist.org/packages/asanzred/payscout
[link-author]: https://github.com/asanzred
[link-contributors]: ../../contributors


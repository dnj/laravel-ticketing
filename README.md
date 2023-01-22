# Laravel Ticketing
 
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]][link-license]
[![Testing status][ico-workflow-test]][link-workflow-test]
 
## Introduction 

This package will help you for manage tickets and support your clients.
* Features include: 
   * Department Management 
   * Ticket Management 
   * Ticket Messages Management 
   * Ticket Attachments Management.
* Latest versions of PHP and PHPUnit and PHPCsFixer
* Best practices applied:
  * [`README.md`][link-readme] (badges included)
  * [`LICENSE`][link-license]
  * [`composer.json`][link-composer-json]
  * [`phpunit.xml`][link-phpunit]
  * [`.gitignore`][link-gitignore]
  * [`.php-cs-fixer.php`][link-phpcsfixer]

## Installation

Require this package with composer.

```shell
composer require dnj/laravel-ticketing
```

Laravel uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.


#### Copy the package config to your local config with the publish command:

```shell
php artisan vendor:publish --provider="dnj\Ticket\TicketServiceProvider"
```

## How to use package API

A document in YAML format has been prepared for better familiarization and use of package web services. which is placed in the [`docs`][link-docs] folder.

To use this file, you can import it on the [stoplight.io](https://stoplight.io) site and see all available web services.


## Contribution

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are greatly appreciated.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement". Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request


## Security
If you discover any security-related issues, please email [hi@dnj.co.ir](hi@dnj.co.ir) instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File][link-license] for more information.


[ico-version]: https://img.shields.io/packagist/v/dnj/laravel-ticketing.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/dnj/laravel-ticketing.svg?style=flat-square
[ico-workflow-test]: https://github.com/dnj/laravel-ticketing/actions/workflows/ci.yaml/badge.svg

[link-workflow-test]: https://github.com/dnj/laravel-ticketing/actions/workflows/ci.yaml
[link-packagist]: https://packagist.org/packages/dnj/laravel-ticketing
[link-license]: https://github.com/dnj/laravel-ticketing/blob/master/LICENSE
[link-downloads]: https://packagist.org/packages/dnj/laravel-ticketing
[link-readme]: https://github.com/dnj/laravel-ticketing/blob/master/README.md
[link-docs]: https://github.com/dnj/laravel-ticketing/blob/master/docs/openapi.yaml
[link-composer-json]: https://github.com/dnj/laravel-ticketing/blob/master/composer.json
[link-phpunit]: https://github.com/dnj/laravel-ticketing/blob/master/phpunit.xml
[link-gitignore]: https://github.com/dnj/laravel-ticketing/blob/master/.gitignore
[link-phpcsfixer]: https://github.com/dnj/laravel-ticketing/blob/master/.php-cs-fixer.php
[link-author]: https://github.com/dnj
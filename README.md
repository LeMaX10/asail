<p align="center"><img src="/art/logo.svg" alt="Logo Laravel Sail"></p>

<p align="center">
    <a href="https://packagist.org/packages/laravel/sail">
        <img src="https://img.shields.io/packagist/dt/laravel/sail" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/laravel/sail">
        <img src="https://img.shields.io/packagist/v/laravel/sail" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/laravel/sail">
        <img src="https://img.shields.io/packagist/l/laravel/sail" alt="License">
    </a>
</p>

## Introduction
Extended Sail provides local Docker-based development for Laravel, compatible with macOS, Windows (WSL2), and Linux. Except for Docker, you don't need to install any programs or libraries on your local machine before using Sail. Sail's simple command line interface means you can start building your Laravel application without any previous Docker experience.

Based on [Laravel Sail](https://github.com/laravel/sail)

#### Inspiration

It was decided to do Extended Sail in view of the lack of support from the authors of outdated versions of laravel, as well as php 7.2, php 7.3.

## Official Documentation

Documentation for Sail can be found on the [Laravel website](https://laravel.com/docs/sail).

## Contributing

Thank you for considering contributing to Sail! You can read the contribution guide [here](.github/CONTRIBUTING.md).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

Please review [our security policy](https://github.com/laravel/sail/security/policy) on how to report security vulnerabilities.

## License

Laravel Advanced Sail is open-sourced software licensed under the [MIT license](LICENSE.md).


## [DEV] Running in octobercms
1. Add repository to composer.json
```json
{
    ...
    "repositories": [
        {
            "type":"vcs",
            "url": "https://github.com/LeMaX10/asail.git"
        }
    ],    
    ...
}
```

2. Add require-dev package in composer.json
```json
{
  ...
  "require-dev": {
     ....
     "lemax10/asail": "1.x-dev"
  }
  ...
}
```

3. Enable discovered package or add service provider to config/app.php
Enable discovered package config/app.php: 
```php
'loadDiscoveredPackages' => true,
```

4. Change october settings to settings in enviroiment:
```bash
php artisan october:env
```

5. Create docker-compose configuration
```bash
php artisan sail:install --with=mysql,redis --project=example.loc --php=7.4
```

6. Run project:
```bash
vendor/bin/sail up -d
```

7. Installed project:
```bash
vendor/bin/sail artisan october:up
```



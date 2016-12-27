# Laravel 5 Interactions

Laravel 5 Interactions, make it a breeze to write interactions, for instance subscribing a newly registered User.

## Installation

### Composer

Execute the following command to get the latest version of the package:

```terminal
composer require sasin91/laravel-interaction
```

Note, to pull this in you might need to set your minimum stability in composer.json
```composer.json
"minimum-stability":"dev",
```

### Laravel

In your `config/app.php` add `Sasin91\LaravelInteractions\RepositoryServiceProvider::class` to the end of the `Package Service Providers` array:

```php
'providers' => [
    ...
    Sasin91\LaravelInteractions\RepositoryServiceProvider::class,
],
```

#### Commands

To generate a interaction, run this command:

```terminal
php artisan make:interaction CreateUser {--contract}
```
generates a contract for the Interaction if the contract option is specified.
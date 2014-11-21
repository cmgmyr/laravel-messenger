[![Build Status](https://travis-ci.org/cmgmyr/laravel-messenger.svg?branch=master)](https://travis-ci.org/cmgmyr/laravel-messenger)

# Laravel Messenger
Simple user messaging tool for Laravel

## Installation
In composer.json:

    "require": {
        "cmgmyr/messenger": "~1.0"
    }

Run:

    composer update

Add the service provider to `app/config/app.php` under `providers`:

    'providers' => [
        'Cmgmyr\Messenger\MessengerServiceProvider'
    ]

Add the trait to your user model:

    use Cmgmyr\Messenger\Traits\Messagable;
    
    class User extends Eloquent {
    	use Messagable;
    }

Migrate your database:

    php artisan migrate --package=cmgmyr/messenger

Move and alter the config file (optional):

    php artisan config:publish cmgmyr/messenger

## Examples
* [Controller](https://github.com/cmgmyr/laravel-messenger/blob/master/src/Cmgmyr/Messenger/examples/MessagesController.php)
* [Routes](https://github.com/cmgmyr/laravel-messenger/blob/master/src/Cmgmyr/Messenger/examples/routes.php)
* [Views](https://github.com/cmgmyr/laravel-messenger/tree/master/src/Cmgmyr/Messenger/examples/views)

## Contributing? 
Please format your code before creating a pull-request:

    vendor/bin/php-cs-fixer fix --level psr2 .

### Special Thanks
This package used [AndreasHeiberg/laravel-messenger](https://github.com/AndreasHeiberg/laravel-messenger) as a starting point.
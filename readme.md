[![Build Status](https://travis-ci.org/cmgmyr/laravel-messenger.svg?branch=master)](https://travis-ci.org/cmgmyr/laravel-messenger)

# Laravel Messenger
Simple user messaging tool for Laravel

## Installation
In composer.json (versioned updates coming soon):

    "cmgmyr/messenger": "dev-master"

Run:

    composer update

Add the service provider to `app/config/app.php` under `providers`:

    'Cmgmyr\Messenger\MessengerServiceProvider'

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
coming soon...

### Special Thanks
This package used [AndreasHeiberg/laravel-messenger](https://github.com/AndreasHeiberg/laravel-messenger) as a starting point.
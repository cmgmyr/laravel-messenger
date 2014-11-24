[![Build Status](https://img.shields.io/travis/cmgmyr/laravel-messenger.svg?style=flat-square)](https://travis-ci.org/cmgmyr/laravel-messenger)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

# Laravel Messenger
This package will allow you to add a full user messaging system into your Laravel application.

## Features
* Multiple conversations per user
* Optionally loop in additional users with each new message
* View the last message for each thread available
* Returns either all messages in the system, all messages associated to the user, or all message associated to the user with new/unread messages
* Return the users unread message count easily
* Very flexible usage so you can implement your own acess control

## Common uses
* Open threads (everyone can see everything)
* Group messaging (only participants can see their threads)
* One to one messaging (private or direct thread)

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
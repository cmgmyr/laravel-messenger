[![Build Status](https://img.shields.io/travis/cmgmyr/laravel-messenger.svg?style=flat-square)](https://travis-ci.org/cmgmyr/laravel-messenger)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/cmgmyr/laravel-messenger.svg?style=flat-square)](https://scrutinizer-ci.com/g/cmgmyr/laravel-messenger/)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/cmgmyr/laravel-messenger.svg?style=flat-square)](https://scrutinizer-ci.com/g/cmgmyr/laravel-messenger/)
[![Latest Version](https://img.shields.io/github/release/cmgmyr/laravel-messenger.svg?style=flat-square)](https://github.com/cmgmyr/laravel-messenger/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/cmgmyr/messenger.svg?style=flat-square)](https://packagist.org/packages/cmgmyr/messenger)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Get help on Codementor](https://cdn.codementor.io/badges/get_help_github.svg)](https://www.codementor.io/cmgmyr)

# Laravel Messenger
This package will allow you to add a full user messaging system into your Laravel application.

## Leave some feedback
[How are you using laravel-messenger?](https://github.com/cmgmyr/laravel-messenger/issues/55)

## Features
* Multiple conversations per user
* Optionally loop in additional users with each new message
* View the last message for each thread available
* Returns either all messages in the system, all messages associated to the user, or all message associated to the user with new/unread messages
* Return the users unread message count easily
* Very flexible usage so you can implement your own access control

## Common uses
* Open threads (everyone can see everything)
* Group messaging (only participants can see their threads)
* One to one messaging (private or direct thread)

## Installation (Laravel 4.x)
Installation instructions for Laravel 4 can be [found here](https://github.com/cmgmyr/laravel-messenger/tree/v1).

## Installation (Laravel 5.x)
```
composer require cmgmyr/messenger
```

Or place manually in composer.json:

```
"require": {
    "cmgmyr/messenger": "~2.0"
}
```

Run:

```
composer update
```

Add the service provider to `config/app.php` under `providers`:

```php
'providers' => [
    Cmgmyr\Messenger\MessengerServiceProvider::class,
],
```

> **Note**: If you are using Laravel 5.5, this step is unnecessary. Laravel Messenger supports [Package Discovery](https://laravel.com/docs/5.5/packages#package-discovery).

Publish config:

```
php artisan vendor:publish --provider="Cmgmyr\Messenger\MessengerServiceProvider" --tag="config"
```
	
Update config file to reference your User Model:

```
config/messenger.php
```

Create a `users` table if you do not have one already. If you need one, the default Laravel migration will be satisfactory.

**(Optional)** Define names of database tables in package config file if you don't want to use default ones:

```php
'messages_table' => 'messenger_messages',
'participants_table' => 'messenger_participants',
'threads_table' => 'messenger_threads',
```
    
Publish migrations:

```
php artisan vendor:publish --provider="Cmgmyr\Messenger\MessengerServiceProvider" --tag="migrations"
```

Migrate your database:

```
php artisan migrate
```

Add the trait to your user model:

```php
use Cmgmyr\Messenger\Traits\Messagable;

class User extends Authenticatable {
    use Messagable;
}
```

## Examples
* [Controller](https://github.com/cmgmyr/laravel-messenger/tree/master/examples/MessagesController.php)
* [Routes](https://github.com/cmgmyr/laravel-messenger/tree/master/examples/routes.php)
* [Views](https://github.com/cmgmyr/laravel-messenger/tree/master/examples/views)

## Example Projects
* [WIP] [Pusher](https://github.com/cmgmyr/laravel-messenger-pusher-demo)
* [WIP] [Lumen API](https://github.com/cmgmyr/lumen-messenger-api)


## Contributing? 
Please format your code before creating a pull-request. This will format all files as specified in `.php_cs`:

```
vendor/bin/php-cs-fixer fix .
```

## Security

If you discover any security related issues, please email [Chris Gmyr](mailto:cmgmyr@gmail.com) instead of using the issue tracker.

## Credits

- [Chris Gmyr](https://github.com/cmgmyr)
- [All Contributors](../../contributors)

### Special Thanks
This package used [AndreasHeiberg/laravel-messenger](https://github.com/AndreasHeiberg/laravel-messenger) as a starting point.

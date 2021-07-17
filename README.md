<p align="center"><a href="https://philo.dev" target="_blank"><img src="https://user-images.githubusercontent.com/1133950/101284994-37e83200-37e3-11eb-9475-3327d204a24f.png" width="150"></a></p>

<p align="center">
<a href="https://github.com/PhiloNL/artisan-remote/actions"><img src="https://github.com/PhiloNL/artisan-remote/workflows/PHPUnit/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/philo/artisan-remote"><img src="https://img.shields.io/packagist/dt/philo/artisan-remote" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/philo/artisan-remote"><img src="https://img.shields.io/packagist/v/philo/artisan-remote" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/philo/artisan-remote"><img src="https://img.shields.io/packagist/l/philo/artisan-remote" alt="License"></a>
</p>

## About Artisan Remote
Artisan Remote is a package for Laravel to interact with your Artisan Commands via an HTTP API.

## Artisan Remote Desktop 
<p align="center"><a href="https://unlock.sh/download/artisan-remote" target="_blank"><img src="https://user-images.githubusercontent.com/1133950/101404659-38fa8b80-38d7-11eb-9fef-89784960b433.png"></a></p>

If you want an easy way to interact with your Artisan commands via the UI, be sure to download the desktop app, which integrates with the Artisan Remote API.

- Execute commands with just a click.
- Provider command parameters via simple input fields.
- Track output of previously executed commands.
- Supports unlimited Laravel application.
- Your application environment details in one single overview. 
- Works on Mac, Windows and Linux.

[Download for Mac, Windows or Linux](https://unlock.sh/download/artisan-remote) distribution via [Unlock](https://unlock.sh).

## Installation
To get started, require the package via Composer:

```
composer require philo/artisan-remote
```

## API endpoints
The environment endpoint will return information about your application environment, like your PHP and Laravel version. 
```json
GET /artisan-remote/environment

Content-Type: application/json
Accept: application/json
Authorization: Bearer 039ede05-d2c1-4ab4-8869-945e805e6bbc

{
  "applicationName": "Unlock.sh",
  "phpVersion": "7.4.12",
  "frameworkVersion": "7.28.1",
  "environment": "production",
  "inMaintenanceMode": true,
  "maxExecutionTime": "30"
}
```

Get a list of the available commands. You can define the commands you want to make available in the `artisan-remote.php` config file.
 
```json
GET /artisan-remote/commands

Content-Type: application/json
Accept: application/json
Authorization: Bearer 039ede05-d2c1-4ab4-8869-945e805e6bbc

[
  {
    "name": "down",
    "description": "Put the application into maintenance mode",
    "arguments": [],
    "options": [
      {
        "name": "message",
        "description": "The message for the maintenance mode",
        "default": null,
        "isArray": false,
        "isRequired": false,
        "isOptional": true
      },
      {
        "name": "retry",
        "description": "The number of seconds after which the request may be retried",
        "default": null,
        "isArray": false,
        "isRequired": false,
        "isOptional": true
      },
      {
        "name": "allow",
        "description": "IP or networks allowed to access the application while in maintenance mode",
        "default": [],
        "isArray": true,
        "isRequired": false,
        "isOptional": true
      }
    ]
  },
  {
    "name": "up",
    "description": "Bring the application out of maintenance mode",
    "arguments": [],
    "options": []
  }
]
```

And finally, you can use the invoke endpoint to execute your command.

```json
POST /artisan-remote/commands/invoke

Content-Type: application/json
Accept: application/json
Authorization: Bearer 039ede05-d2c1-4ab4-8869-945e805e6bbc

{
  "name": "down",
  "options": {
    "message": "We will be back in 15 minutes",
    "retry": "900",
    "allow": [
      "127.0.0.1"
    ]
  }
}

// Response
{
  "rawCommandOutput": "\u001b[33mApplication is now in maintenance mode.\u001b[39m\n",
  "HtmlCommandOutput": "<span style=\"background-color: transparent; color: #e5e510\">Application is now in maintenance mode.<\/span><span style=\"background-color: transparent; color: #e5e5e5\">\n<\/span>",
  "exitCode": 0,
  "executionTime": 0.017518997192382812
}
```

## API authorization
Before you can make requests to the API endpoints, you need to set up authorization. You can authorize access to specific commands in the `artisan-remote.php` config file. For example, you might want to allow your client to run the artisan up and down commands.

```php
<?php

return [
    'commands'     => [
        \Illuminate\Foundation\Console\UpCommand::class,
        \Illuminate\Foundation\Console\DownCommand::class,
        \Illuminate\Cache\Console\ClearCommand::class,
    ],
    'auth'         => [
        // This API token will be able to access only the up and down command.
        '79e9ab08-bdc0-4bef-8af2-5e5b5579f9af' => [
            \Illuminate\Foundation\Console\UpCommand::class,
            \Illuminate\Foundation\Console\DownCommand::class,
        ],
        // This API token will be able to access the up, down and cache:clear command.
        '3c562cb3-62ba-4fe4-9875-528ecae6e8b4' => ['*'],
    ],
    'route_prefix' => 'artisan-remote',
];
```

It's best practice not to include any credentials directly in your configuration file, so make sure to use environment variables in production.

```php
// artisan-remote.php
'auth'         => [
    env('CLIENT_ARTISAN_REMOTE_API_KEY') => [
        \Illuminate\Foundation\Console\UpCommand::class,
        \Illuminate\Foundation\Console\DownCommand::class,
    ],
]

// .env
CLIENT_ARTISAN_REMOTE_API_KEY=79e9ab08-bdc0-4bef-8af2-5e5b5579f9af
```


##### Running commands when your application is in maintenance mode
By default, you will not be able to execute commands when your application is in maintenance. To run commands while your application is in maintenance mode, you will need to adjust the CheckForMaintenanceMode middleware.

```php
// app/Http/Middleware/CheckForMaintenanceMode.php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;

class CheckForMaintenanceMode extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [
        'artisan-remote/*'
    ];
}
```
 

## Credits
- [Philo Hermans](https://github.com/philoNL)
- [All Contributors](../../contributors)

## License
Artisan Remote is open-sourced software licensed under the [MIT license](LICENSE.md).

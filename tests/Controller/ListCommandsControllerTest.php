<?php declare(strict_types=1);

namespace Philo\ArtisanRemote\Tests\Controller;

use Illuminate\Foundation\Console\RouteListCommand;
use Philo\ArtisanRemote\Tests\TestCase;
use Illuminate\Foundation\Console\DownCommand;
use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Foundation\Console\UpCommand;

class ListCommandsControllerTest extends TestCase
{
    public function test_it_returns_available_commands()
    {
        $this->handleValidationExceptions();
        $this->overrideArtisanRemoteCommands([
            DownCommand::class,
            UpCommand::class,
            ModelMakeCommand::class,
            RouteListCommand::class,
        ]);

        $this->overrideArtisanRemoteAuthentication([
            '039ede05-d2c1-4ab4-8869-945e805e6bbc' => [
                UpCommand::class,
                ModelMakeCommand::class,
            ],
        ]);

        $response = $this->withToken('039ede05-d2c1-4ab4-8869-945e805e6bbc')->get('artisan-remote/commands');
        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJson([
            [
                'name'        => 'up',
                'description' => 'Bring the application out of maintenance mode',
                'arguments'   => [],
                'options'     => [],
            ],
            [
                'name'        => 'make:model',
                'description' => 'Create a new Eloquent model class',
                'arguments'   => [
                    [
                        'name'        => 'name',
                        'description' => 'The name of the class',
                        'default'     => null,
                        'isRequired'  => true,
                        'isArray'     => false,
                    ],
                ],
                'options'     => [
                    [
                        'name'        => 'all',
                        'description' => 'Generate a migration, seeder, factory, and resource controller for the model',
                        'default'     => false,
                        'isArray'     => false,
                        'isRequired'  => false,
                        'isOptional'  => false,
                    ],
                    [
                        'name'        => 'controller',
                        'description' => 'Create a new controller for the model',
                        'default'     => false,
                        'isArray'     => false,
                        'isRequired'  => false,
                        'isOptional'  => false,
                    ],
                    [
                        'name'        => 'factory',
                        'description' => 'Create a new factory for the model',
                        'default'     => false,
                        'isArray'     => false,
                        'isRequired'  => false,
                        'isOptional'  => false,
                    ],
                    [
                        'name'        => 'force',
                        'description' => 'Create the class even if the model already exists',
                        'default'     => false,
                        'isArray'     => false,
                        'isRequired'  => false,
                        'isOptional'  => false,
                    ],
                    [
                        'name'        => 'migration',
                        'description' => 'Create a new migration file for the model',
                        'default'     => false,
                        'isArray'     => false,
                        'isRequired'  => false,
                        'isOptional'  => false,
                    ],
                    [
                        'name'        => 'seed',
                        'description' => 'Create a new seeder file for the model',
                        'default'     => false,
                        'isArray'     => false,
                        'isRequired'  => false,
                        'isOptional'  => false,
                    ],
                    [
                        'name'        => 'pivot',
                        'description' => 'Indicates if the generated model should be a custom intermediate table model',
                        'default'     => false,
                        'isArray'     => false,
                        'isRequired'  => false,
                        'isOptional'  => false,
                    ],
                    [
                        'name'        => 'resource',
                        'description' => 'Indicates if the generated controller should be a resource controller',
                        'default'     => false,
                        'isArray'     => false,
                        'isRequired'  => false,
                        'isOptional'  => false,
                    ],
                    [
                        'name'        => 'api',
                        'description' => 'Indicates if the generated controller should be an API controller',
                        'default'     => false,
                        'isArray'     => false,
                        'isRequired'  => false,
                        'isOptional'  => false,
                    ],
                ],
            ],
        ]);
    }

    public function test_request_fails_with_invalid_token()
    {
        $response = $this->withToken('invalid-token')->get('artisan-remote/commands');
        $response->assertForbidden();
    }
}
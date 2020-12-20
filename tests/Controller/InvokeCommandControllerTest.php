<?php declare(strict_types=1);

namespace Philo\ArtisanRemote\Tests\Controller;

use Philo\ArtisanRemote\Tests\TestCase;
use Illuminate\Foundation\Console\UpCommand;

class InvokeCommandControllerTest extends TestCase
{
    public function test_if_command_is_invoked()
    {
        $this->overrideArtisanRemoteCommands([
            UpCommand::class,
        ]);

        $this->overrideArtisanRemoteAuthentication([
            '039ede05-d2c1-4ab4-8869-945e805e6bbc' => '*',
        ]);

        $response = $this->withToken('039ede05-d2c1-4ab4-8869-945e805e6bbc')
            ->postJson('artisan-remote/commands/invoke', [
                'name' => 'up',
            ]);

        $response->assertOk();
        $response->assertJson([
            'exitCode' => 1,
        ]);
        $response->assertSee('Application is already up.');
        $response->assertJsonStructure([
            'rawCommandOutput',
            'HtmlCommandOutput',
            'exitCode',
            'executionTime',
        ]);
    }

    public function test_if_commands_accepts_arguments()
    {
        $this->registerExampleCommand();

        $this->overrideArtisanRemoteAuthentication([
            '039ede05-d2c1-4ab4-8869-945e805e6bbc' => '*',
        ]);

        $response = $this->withToken('039ede05-d2c1-4ab4-8869-945e805e6bbc')
            ->postJson('artisan-remote/commands/invoke', [
                'name'      => 'example',
                'arguments' => [
                    'requiredArgument' => 'Foobar',
                ],
                'options'   => [
                    'optionWithArray' => ['one', 'two', 'three'],
                ],
            ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'rawCommandOutput',
            'HtmlCommandOutput',
            'exitCode',
            'executionTime',
        ]);
        $response->assertJson(['exitCode' => 0]);
        $response->assertSee('Executing example command...');
        $response->assertSee('Execution completed...');
    }

    public function test_if_validation_error_is_thrown_if_command_doesnt_exist()
    {
        $this->overrideArtisanRemoteAuthentication([
            '039ede05-d2c1-4ab4-8869-945e805e6bbc' => '*',
        ]);

        $response = $this->withToken('039ede05-d2c1-4ab4-8869-945e805e6bbc')
            ->postJson('artisan-remote/commands/invoke', [
                'name' => 'non-existing-command',
            ]);

        $response->assertJsonValidationErrors(['name']);
    }

    public function test_if_validation_error_is_thrown_if_required_argument_is_missing()
    {
        $this->registerExampleCommand();

        $this->overrideArtisanRemoteAuthentication([
            '039ede05-d2c1-4ab4-8869-945e805e6bbc' => '*',
        ]);

        $response = $this->withToken('039ede05-d2c1-4ab4-8869-945e805e6bbc')
            ->postJson('artisan-remote/commands/invoke', [
                'name' => 'example',
            ]);

        $response->assertJsonValidationErrors(['arguments.requiredArgument']);
    }

    public function test_if_validation_error_is_thrown_if_array_is_required_but_string_is_given()
    {
        $this->registerExampleCommand();

        $this->overrideArtisanRemoteAuthentication([
            '039ede05-d2c1-4ab4-8869-945e805e6bbc' => '*',
        ]);

        $response = $this->withToken('039ede05-d2c1-4ab4-8869-945e805e6bbc')
            ->postJson('artisan-remote/commands/invoke', [
                'name'      => 'example',
                'arguments' => [
                    'requiredArgument' => 'Foobar',
                ],
                'options'   => [
                    'optionWithArray' => 'some-string',
                ],
            ]);

        $response->assertJsonValidationErrors(['options.optionWithArray']);
    }

    public function test_request_fails_with_invalid_token()
    {
        $response = $this->withToken('invalid-token')->post('artisan-remote/commands/invoke');
        $response->assertForbidden();
    }
}
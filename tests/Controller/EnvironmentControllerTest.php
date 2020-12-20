<?php declare(strict_types=1);

namespace Philo\ArtisanRemote\Tests\Controller;

use Philo\ArtisanRemote\Tests\TestCase;

class EnvironmentControllerTest extends TestCase
{
    public function test_if_environment_data_is_returned()
    {
        $this->overrideArtisanRemoteAuthentication([
            '039ede05-d2c1-4ab4-8869-945e805e6bbc' => '*',
        ]);

        $response = $this->withToken('039ede05-d2c1-4ab4-8869-945e805e6bbc')
            ->getJson('artisan-remote/environment');

        $response->assertJson([
            'applicationName'   => 'mysite',
            'environment'       => 'testing',
            'inMaintenanceMode' => false,
            'maxExecutionTime'  => 0,
        ]);

        $response->assertJsonStructure([
            'applicationName',
            'phpVersion',
            'frameworkVersion',
            'environment',
            'inMaintenanceMode',
            'maxExecutionTime',
        ]);
    }

    public function test_request_fails_with_invalid_token()
    {
        $response = $this->withToken('invalid-token')->get('artisan-remote/environment');
        $response->assertForbidden();
    }
}
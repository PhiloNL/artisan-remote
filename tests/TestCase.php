<?php declare(strict_types=1);

namespace Philo\ArtisanRemote\Tests;

use Philo\ArtisanRemote\ArtisanRemoteServiceProvider;
use Philo\ArtisanRemote\Tests\Stubs\ExampleCommand;
use Illuminate\Console\Application;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ArtisanRemoteServiceProvider::class,
        ];
    }

    protected function registerExampleCommand()
    {
        $this->app->singleton('command.example', function ($app) {
            return new ExampleCommand();
        });

        Application::starting(function ($artisan) {
            $artisan->resolveCommands(['command.example']);
        });

        $this->overrideArtisanRemoteCommands([
            ExampleCommand::class,
        ]);
    }

    protected function overrideArtisanRemoteCommands(array $commands)
    {
        config()->set('artisan-remote.commands', $commands);
    }

    protected function overrideArtisanRemoteAuthentication(array $auth)
    {
        config()->set('artisan-remote.auth', $auth);
    }
}
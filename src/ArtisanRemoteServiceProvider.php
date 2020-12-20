<?php declare(strict_types=1);

namespace Philo\ArtisanRemote;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Philo\ArtisanRemote\Http\Middleware\Authenticate;

class ArtisanRemoteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/artisan-remote.php' => config_path('artisan-remote.php'),
        ], 'config');

        $this->registerRoutes();
        $this->authenticate();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/artisan-remote.php', 'artisan-remote'
        );
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });
    }

    /**
     * Get the Nova route group configuration array.
     *
     * @return array
     */
    protected function routeConfiguration(): array
    {
        return [
            'namespace'  => 'Philo\ArtisanRemote\Http\Controllers',
            'prefix'     => config('artisan-remote.route_prefix', 'artisan-remote'),
            'middleware' => Authenticate::class,
        ];
    }

    private function authenticate(): void
    {
        ArtisanRemote::auth(function (Request $request) {
            return app()->environment('local') || array_key_exists($request->bearerToken(),
                    config('artisan-remote.auth', true));
        });
    }
}
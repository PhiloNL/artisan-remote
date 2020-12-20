<?php declare(strict_types=1);

namespace Philo\ArtisanRemote\Http\Controllers;

class EnvironmentController
{
    public function __invoke()
    {
        return [
            'applicationName'  => config('app.name', 'Laravel'),
            'phpVersion'       => phpversion(),
            'frameworkVersion' => app()->version(),
            'environment'      => app()->environment(),
            'inMaintenanceMode' => app()->isDownForMaintenance(),
            'maxExecutionTime' => ini_get('max_execution_time'),
        ];
    }
}
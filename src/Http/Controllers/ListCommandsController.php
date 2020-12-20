<?php declare(strict_types=1);

namespace Philo\ArtisanRemote\Http\Controllers;

use Illuminate\Http\Request;
use Philo\ArtisanRemote\ArtisanRemote;
use Illuminate\Console\Command;
use Illuminate\Routing\Controller;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ListCommandsController extends Controller
{
    public function __invoke(Request $request)
    {
        return ArtisanRemote::availableCommandsForToken($request->bearerToken())->map(function (Command $command) {
            return [
                'name'        => $command->getName(),
                'description' => $command->getDescription(),
                'arguments'   => collect($command->getDefinition()->getArguments())->values()->map(function (
                    InputArgument $option
                ) {
                    return [
                        'name'        => $option->getName(),
                        'description' => $option->getDescription(),
                        'default'     => $option->getDefault(),
                        'isRequired'  => $option->isRequired(),
                        'isArray'     => $option->isArray(),
                    ];
                }),
                'options'     => collect($command->getDefinition()->getOptions())->map(function (InputOption $option) {
                    return [
                        'name'        => $option->getName(),
                        'description' => $option->getDescription(),
                        'default'     => $option->getDefault(),
                        'isArray'     => $option->isArray(),
                        'isRequired'  => $option->isValueRequired(),
                        'isOptional'  => $option->isValueOptional(),
                    ];
                })->values(),
            ];
        })->values();
    }
}

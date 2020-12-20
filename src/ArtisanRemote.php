<?php declare(strict_types=1);

namespace Philo\ArtisanRemote;

use Closure;
use Philo\ArtisanRemote\Theme\DefaultTheme;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ArtisanRemote
{
    /**
     * The callback that should be used to authenticate Remote Artisan requests.
     *
     * @var \Closure
     */
    public static $authUsing;

    public static function availableCommands(): Collection
    {
        return collect(Artisan::all())->filter(function ($command) {
            return in_array(get_class($command), config('artisan-remote.commands'));
        });
    }

    public static function availableCommandsForToken(string $bearerToken)
    {
        return static::availableCommands()->filter(function (Command $command) use ($bearerToken) {
            $authorizedCommands = (array)config("artisan-remote.auth.{$bearerToken}");

            if ($authorizedCommands === ['*']) {
                return true;
            }

            return in_array(get_class($command), $authorizedCommands, true);
        });
    }

    public static function getCommandByName($name): Command
    {
        return static::availableCommands()->get($name);
    }

    public static function hasCommand($name): bool
    {
        return static::availableCommands()->has($name);
    }

    public static function getCommandArgumentsValidationRules(string $name): Collection
    {
        return collect(static::getCommandByName($name)->getDefinition()->getArguments())
            ->mapWithKeys(function (InputArgument $argument) {
                $rules = [];

                if ($argument->isRequired()) {
                    array_push($rules, 'required');
                }

                if ($argument->isArray()) {
                    array_push($rules, 'array');
                }

                return ["arguments.{$argument->getName()}" => $rules];
            })->filter();
    }

    public static function getCommandOptionsValidationRules(string $name): Collection
    {
        return collect(static::getCommandByName($name)->getDefinition()->getOptions())
            ->mapWithKeys(function (InputOption $option) {
                $rules = [];

                if ($option->isValueRequired()) {
                    array_push($rules, 'required');
                }

                if ($option->isArray()) {
                    array_push($rules, 'array');
                }

                return ["options.{$option->getName()}" => $rules];
            })->filter();
    }

    public static function getCommandValidationRules(string $name): Collection
    {
        return collect()
            ->merge(static::getCommandArgumentsValidationRules($name))
            ->merge(static::getCommandOptionsValidationRules($name));
    }

    public static function callByInvokeRequest(Http\Requests\InvokeCommandRequest $request)
    {
        $command = static::getCommandByName($request->name());

        $argumentsAndOptions = collect($request->options())
            ->mapWithKeys(function ($optionValue, $optionName) {
                return ["--{$optionName}" => $optionValue];
            })
            ->merge($request->arguments())
            ->put('--ansi', true);

        $start = microtime(true);
        $exitCode = Artisan::call($command->getName(), $argumentsAndOptions->toArray());
        $end = microtime(true);

        $output = Artisan::output();
        $theme = new DefaultTheme();
        $converter = new AnsiToHtmlConverter($theme);
        $html = $converter->convert($output);

        return [
            'rawCommandOutput'  => $output,
            'HtmlCommandOutput' => $html,
            'exitCode'          => $exitCode,
            'executionTime'     => $end - $start,
        ];
    }

    public static function check($request)
    {
        return (static::$authUsing ?: function () {
            return app()->environment('local');
        })($request);
    }

    public static function auth(Closure $callback)
    {
        static::$authUsing = $callback;

        return new static;
    }
}
<?php declare(strict_types=1);

namespace Philo\ArtisanRemote\Http\Requests;

use Philo\ArtisanRemote\ArtisanRemote;
use Philo\ArtisanRemote\Http\Rules\ValidCommand;
use Illuminate\Foundation\Http\FormRequest;

class InvokeCommandRequest extends FormRequest
{
    public function rules()
    {
        return array_merge([
            'name' => ['required', new ValidCommand],
        ], optional(ArtisanRemote::hasCommand($this->name()), function ($hasCommand) {
            return $hasCommand ? ArtisanRemote::getCommandValidationRules($this->name())->toArray() : [];
        }));
    }

    public function name(): ?string
    {
        return $this->json('name');
    }

    public function arguments()
    {
        return $this->json('arguments');
    }

    public function options()
    {
        return $this->json('options');
    }
}
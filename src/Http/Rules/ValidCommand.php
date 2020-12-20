<?php declare(strict_types=1);

namespace Philo\ArtisanRemote\Http\Rules;

use Philo\ArtisanRemote\ArtisanRemote;
use Illuminate\Contracts\Validation\Rule;

class ValidCommand implements Rule
{
    public function passes($attribute, $value)
    {
        return ArtisanRemote::hasCommand($value) == true;
    }

    public function message()
    {
        return 'The command ":input" does not exist.';
    }
}
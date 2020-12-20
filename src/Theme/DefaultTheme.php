<?php declare(strict_types=1);

namespace Philo\ArtisanRemote\Theme;

use SensioLabs\AnsiConverter\Theme\Theme;

class DefaultTheme extends Theme
{
    public function asArray()
    {
        return [
            'black'   => 'transparent',
            'red'     => '#cd3131',
            'green'   => '#0DBC79',
            'yellow'  => '#e5e510',
            'blue'    => '#2472c8',
            'magenta' => '#bc3fbc',
            'cyan'    => '#11a8cd',
            'white'   => '#e5e5e5',

            'brblack'   => '#666666',
            'brred'     => '#f14c4c',
            'brgreen'   => '#23d18b',
            'bryellow'  => '#f5f543',
            'brblue'    => '#3b8eea',
            'brmagenta' => '#d670d6',
            'brcyan'    => '#29b8db',
            'brwhite'   => '#ffffff',
        ];
    }
}
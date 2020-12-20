<?php declare(strict_types=1);

namespace Philo\ArtisanRemote\Http\Controllers;

use Philo\ArtisanRemote\ArtisanRemote;
use Philo\ArtisanRemote\Http\Requests\InvokeCommandRequest;
use Illuminate\Routing\Controller;

class InvokeCommandController extends Controller
{
    public function __invoke(InvokeCommandRequest $request)
    {
        return ArtisanRemote::callByInvokeRequest($request);
    }
}
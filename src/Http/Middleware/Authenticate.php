<?php declare(strict_types=1);

namespace Philo\ArtisanRemote\Http\Middleware;

use Closure;
use Philo\ArtisanRemote\ArtisanRemote;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return ArtisanRemote::check($request) ? $next($request) : abort(403);
    }
}
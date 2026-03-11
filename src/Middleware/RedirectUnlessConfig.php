<?php

namespace Beholdr\LaravelHelpers\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectUnlessConfig
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $configOption, string $address): Response
    {
        if (config($configOption)) {
            return $next($request);
        }

        return app(Redirect::class)->handle($request, fn ($request) => $next($request), $address);
    }
}

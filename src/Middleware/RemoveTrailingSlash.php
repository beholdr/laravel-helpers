<?php

namespace Beholdr\LaravelHelpers\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

// https://stefanbauer.me/tips-and-tricks/a-middleware-for-removing-trailing-slashes
class RemoveTrailingSlash
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (preg_match('/.+\/$/', $request->getRequestUri())) {
            return Redirect::to(rtrim($request->getRequestUri(), '/'), 301);
        }

        return $next($request);
    }
}

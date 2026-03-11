<?php

namespace Beholdr\LaravelHelpers\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
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
        $url = Str::of($request->getRequestUri());

        if ($url->endsWith('/') || $url->contains('/?') || $url->contains('//')) {
            $uri = $request->uri();
            $path = empty((string) $uri->query())
                ? $uri->path()
                : $uri->path().'?'.$uri->query();
            $path = '/'.Str::deduplicate($path, '/');

            return Redirect::to($path, 301);
        }

        return $next($request);
    }
}

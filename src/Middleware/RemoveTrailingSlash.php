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
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $url = Str::of($request->getRequestUri());
        $uri = $request->uri();

        $endsWithSlash = ($uri->path() !== '/') && $url->endsWith('/');
        $endsWithSlashQuery = ($uri->path() !== '/') && $url->contains('/?');

        if ($endsWithSlash || $endsWithSlashQuery || $url->contains('//')) {
            $path = Str::deduplicate($uri->path(), '/');
            if ((string) $uri->query() !== '') {
                $path .= '?'.$uri->query();
            }
            $path = Str::start($path, '/');

            return Redirect::to($path, 301);
        }

        return $next($request);
    }
}

<?php

namespace Beholdr\LaravelHelpers\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermanentRedirects
{
    /**
     * Handle an incoming request.
     * Source: https://github.com/mcamara/laravel-localization/issues/881#issuecomment-2076641534
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof RedirectResponse && $response->getStatusCode() === Response::HTTP_FOUND) {
            // keep 302 for domain root
            if ($request->path() == '/') {
                return $response;
            }

            $response->setStatusCode(Response::HTTP_MOVED_PERMANENTLY);
        }

        return $response;
    }
}

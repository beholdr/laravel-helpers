<?php

namespace Beholdr\LaravelHelpers\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RemoveIndex
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Str::endsWith($request->getRequestUri(), '/index')) {
            return Redirect::to(rtrim($request->getRequestUri(), '/index'), 301);
        }

        return $next($request);
    }
}

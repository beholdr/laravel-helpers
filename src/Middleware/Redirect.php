<?php

namespace Beholdr\LaravelHelpers\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class Redirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $address): Response
    {
        $isUrl = Str::isUrl($address) || Str::startsWith($address, '/');
        $url = $isUrl ? $address : route($address);

        if (! empty($request->query())) {
            $url .= '?'.Arr::query($request->query());
        }

        return redirect($url, Response::HTTP_MOVED_PERMANENTLY);
    }
}

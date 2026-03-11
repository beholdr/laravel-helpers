<?php

namespace Beholdr\LaravelHelpers\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SimpleAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $username, string $password): Response
    {
        if ($request->getUser() !== $username || $request->getPassword() !== $password) {
            return response('Please authorize', Response::HTTP_UNAUTHORIZED)
                ->header('WWW-Authenticate', 'Basic realm="Restricted Area"');
        }

        return $next($request);
    }
}

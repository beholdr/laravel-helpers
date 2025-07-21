<?php

namespace Beholdr\LaravelHelpers\Middleware;

use Beholdr\LaravelHelpers\Attributes\Embeddable;
use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Livewire\Exceptions\ComponentNotFoundException;
use Livewire\LivewireManager;
use Livewire\Mechanisms\ComponentRegistry;
use ReflectionClass;

// Based on https://github.com/wire-elements/wire-extender/blob/main/src/Http/Middlewares/IgnoreForWireExtender.php
class IgnoreEmbeddableCsrfToken extends VerifyCsrfToken
{
    public function handle($request, Closure $next)
    {
        // We only care about requests from an embedded component
        if (! $this->isLivewireUpdateRequest($request)) {
            return parent::handle($request, $next);
        }

        // Loop through all components that are part of the update
        foreach ($request->json('components', []) as $component) {
            $snapshot = json_decode($component['snapshot'], true);
            $component = $snapshot['memo']['name'] ?? false;

            // All components must be embeddable otherwise we will apply the existing middleware
            if ($this->isEmbeddable($component) === false) {
                return parent::handle($request, $next);
            }
        }

        return $next($request);
    }

    private function isLivewireUpdateRequest($request): bool
    {
        return $request->method() === 'POST' &&
            app(LivewireManager::class)->getUpdateUri() === $request->getRequestUri() &&
            $request->hasHeader('X-Livewire');
    }

    private function isEmbeddable($component): bool
    {
        try {
            $reflectionClass = new ReflectionClass(app(ComponentRegistry::class)->new($component));
            $embedAttribute = $reflectionClass->getAttributes(Embeddable::class)[0] ?? null;

            return is_null($embedAttribute) === false;
        } catch (ComponentNotFoundException) {
            return false;
        }
    }
}

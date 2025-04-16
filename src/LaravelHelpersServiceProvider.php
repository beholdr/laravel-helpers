<?php

namespace Beholdr\LaravelHelpers;

use Beholdr\LaravelHelpers\Listeners\HttpClientLog;
use Illuminate\Http\Client\Events\ConnectionFailed;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelHelpersServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-helpers')
            ->hasConfigFile();
    }

    public function packageBooted()
    {
        if (config('helpers.http_client_log')) {
            Event::listen(ConnectionFailed::class, HttpClientLog::class);
            Event::listen(ResponseReceived::class, HttpClientLog::class);
        }
    }
}

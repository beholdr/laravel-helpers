# Laravel helpers

Some helpers for Laravel projects.

## Installation

You can install the package via composer:

```bash
composer require beholdr/laravel-helpers
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="helpers-config"
```

This is the contents of the published config file:

```php
return [
    'http_client_log' => true,
];
```

## Usage

### Redirect middleware

Simple redirect middleware.

Add an alias in `bootstrap/app.php`:

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            // other middleware aliases...
            'redirect' => \Beholdr\LaravelHelpers\Middleware\Redirect::class,
        ]);
```

Example of usage in Folio page:

```php
<?php

use function Laravel\Folio\middleware;

// redirect by route name
middleware(['redirect:route.cards.unistream']);

// OR redirect by URL
middleware(['redirect:/']);

?>
```

### PermanentRedirects middleware

Replaces all `302` redirects with `301` (for SEO purposes).

Add in `bootstrap/app.php`:

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \Beholdr\LaravelHelpers\Middleware\PermanentRedirects::class,
        ]);
```

### RemoveIndex middleware

Removes trailing `/index` from URLs, making a redirect `/url/index` → `/url`.
Useful for folio pages.

Add in `bootstrap/app.php`:

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \Beholdr\LaravelHelpers\Middleware\RemoveIndex::class,
        ]);
```

### RemoveTrailingSlash middleware

Removes trailing slashes from URLs, making a redirect `/some/url/` → `/some/url`.

Add in `bootstrap/app.php`:

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \Beholdr\LaravelHelpers\Middleware\RemoveTrailingSlash::class,
        ]);
```

### FromUrl attribute

When you need to load initial value of the Livewire property from the URL, but do not need to update URL on property change:

```php
use Beholdr\LaravelHelpers\Attributes\FromUrl;

class Calculator extends Component
{
    #[FromUrl]
    public $country;
}
```


### UtmFields enum

Enum `UtmFields` is used for processing of UTM analytics tags.

To get an array of UTM parameters, exluding all other query variables:

```php
use Beholdr\LaravelHelpers\Enums\UtmFields;

UtmFields::fromQuery(request()->getQueryString()); // ['utm_content' => '...', 'utm_source' => '...']
```

### HttpClient logger

Automatically logs all HttpClient requests: both success and failure.
Can be disabled via `http_client_log` config option.

### Telegram log alerts

Custom log channel `TelegramLogChannel` sends alert to your telegram bot upon a log event with a defined level.

Add in your `config/logging.php`:

```php
'channels' => [
    // other channels...
    'telegram' => [
        'driver' => 'custom',
        'via' => \Beholdr\LaravelHelpers\Logging\TelegramLogChannel::class,
        'token' => env('TELEGRAM_BOT_TOKEN'),
        'channel' => env('TELEGRAM_CHAT_ID'),
        'level' => env('TELEGRAM_LOG_LEVEL', \Monolog\Level::Error),
    ],
]
```

And then define in your `.env`:

```
LOG_STACK=daily,telegram

TELEGRAM_BOT_TOKEN=#####
TELEGRAM_CHAT_ID=#####
```

Where `TELEGRAM_BOT_TOKEN` and `TELEGRAM_CHAT_ID` contains credentials for your [telegram bot](https://core.telegram.org/bots) and [channel ID](https://gist.github.com/mraaroncruz/e76d19f7d61d59419002db54030ebe35).

### AppException

Universal exception class `Beholdr\LaravelHelpers\Exceptions\AppException` to wrap other exceptions and forward to a client.

Can define HTTP `statusCode` (`500` by default) and disable reporting in logs.

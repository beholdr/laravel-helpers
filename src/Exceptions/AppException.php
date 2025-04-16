<?php

namespace Beholdr\LaravelHelpers\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

// src https://jessarcher.com/articles/httpable-exceptions-in-laravel/
class AppException extends Exception implements HttpExceptionInterface
{
    public function __construct(
        protected $message,
        protected $errorCode = null,
        protected $statusCode = 500,
        protected bool $silent = true,
    ) {}

    public function report(): bool
    {
        // when true, exception not showed in logs (custom reporting mode)
        return $this->silent;
    }

    public function getErrorCode(): string|int|null
    {
        return $this->errorCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return [];
    }
}

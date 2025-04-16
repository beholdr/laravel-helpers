<?php

namespace Beholdr\LaravelHelpers\Logging;

use Monolog\Level;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class TelegramProcessor implements ProcessorInterface
{
    public function __invoke(LogRecord $record): LogRecord
    {
        $record->extra['app'] = config('app.name');
        $record->extra['environment'] = app()->environment();

        $record->extra['icon'] = match ($record->level) {
            Level::Debug => '🛠️',
            Level::Info => 'ℹ️',
            Level::Notice => '🟠',
            Level::Warning => '⚠️',
            Level::Error => '❗',
            Level::Critical => '‼️',
            Level::Alert => '🆘',
            Level::Emergency => '🆘',
        };

        return $record;
    }
}

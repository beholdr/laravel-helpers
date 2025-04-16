<?php

namespace Beholdr\LaravelHelpers\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\DeduplicationHandler;
use Monolog\Handler\TelegramBotHandler;
use Monolog\Logger;
use Monolog\Processor\WebProcessor;

class TelegramLogChannel
{
    public function __invoke(array $config)
    {
        $telegramHandler = new TelegramBotHandler(
            $config['token'],
            $config['channel'],
            $config['level'],
        );

        $telegramHandler->pushProcessor(new WebProcessor);
        $telegramHandler->pushProcessor(new TelegramProcessor);

        $formatter = new LineFormatter(
            "%extra.icon% %extra.app% (%extra.environment%):\n[%datetime%] %channel%.%level_name%: %message% %context% %extra%",
            'Y-m-d H:i:s',
            allowInlineLineBreaks: true,
        );
        $telegramHandler->setFormatter($formatter);

        $deduplicationHandler = new DeduplicationHandler($telegramHandler);

        $logger = new Logger('telegram');
        $logger->pushHandler($deduplicationHandler);

        return $logger;
    }
}

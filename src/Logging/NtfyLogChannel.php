<?php

namespace Beholdr\LaravelHelpers\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\DeduplicationHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;

final class NtfyLogChannel
{
    public function __invoke(array $config): Logger
    {
        $server = rtrim($config['server'], '/');

        $handler = new class($server, $config['topic'], $config['level']) extends AbstractProcessingHandler
        {
            public function __construct(
                private readonly string $server,
                private readonly string $topic,
                Level $level,
            ) {
                parent::__construct($level);
            }

            protected function write(LogRecord $record): void
            {
                $curl = curl_init("{$this->server}/{$this->topic}");
                $title = sprintf('%s log [%s]', config('app.name'), $record->level->getName());
                $headers = [
                    'Content-Type: text/plain; charset=utf-8',
                    'Title: '.$title,
                    'Priority: '.$this->priority($record->level),
                    'Markdown: yes',
                    'Tags: '.$this->tag($record->level),
                ];

                curl_setopt_array($curl, [
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $this->formatMessage($record),
                    CURLOPT_HTTPHEADER => $headers,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 3,
                    CURLOPT_CONNECTTIMEOUT => 3,
                ]);

                curl_exec($curl);
                curl_close($curl);
            }

            private function formatMessage(LogRecord $record): string
            {
                $message = sprintf(
                    "[%s] %s\n\n```\n%s\n```",
                    $record->channel,
                    $record->message,
                    json_encode(
                        $record->context,
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                    ),
                );

                if ($record->extra !== []) {
                    $message .= "\n```\n".json_encode(
                        $record->extra,
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                    )."\n```";
                }

                return $message;
            }

            private function priority(Level $level): int
            {
                return match ($level) {
                    Level::Emergency => 5,
                    Level::Alert => 5,
                    Level::Critical => 5,
                    Level::Error => 4,
                    Level::Warning => 3,
                    Level::Notice => 2,
                    default => 1,
                };
            }

            private function tag(Level $level): string
            {
                return match ($level) {
                    Level::Emergency => 'red_circle',
                    Level::Alert => 'red_circle',
                    Level::Critical => 'red_circle',
                    Level::Error => 'red_circle',
                    Level::Warning => 'orange_circle',
                    Level::Notice => 'yellow_circle',
                    default => 'large_blue_circle',
                };
            }
        };

        $deduplicationHandler = new DeduplicationHandler($handler, time: $config['deduplication_time'] ?? 60);

        $logger = new Logger('ntfy');
        $logger->pushHandler($deduplicationHandler);

        return $logger;
    }
}

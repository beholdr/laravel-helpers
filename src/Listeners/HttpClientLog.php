<?php

namespace Beholdr\LaravelHelpers\Listeners;

use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Http\Client\Events\ConnectionFailed;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClientLog
{
    protected const TEMPLATE = '>>>\n{request}\n<<<\n{response}';

    protected const RESPONSE_LIMIT = 1024;

    public function handle(ConnectionFailed|ResponseReceived $event)
    {
        if ($event instanceof ConnectionFailed) {
            Log::warning('[HttpClient] ConnectionFailed', ['info' => $this->format($event->request->toPsrRequest())]);

            return;
        }

        $context = [
            'log' => $this->format($event->request->toPsrRequest(), $event->response->toPsrResponse()),
            'time' => $event->response->transferStats->getTransferTime(),
        ];

        $event->response->successful()
            ? Log::debug('[HttpClient] Success', $context)
            : Log::warning('[HttpClient] Error', $context);
    }

    private static function format(RequestInterface $request, ?ResponseInterface $response = null)
    {
        // trim big request
        if ($request->hasHeader('content-type') && Str::contains($request->getHeaderLine('content-type'), 'multipart/form-data')) {
            $request = $request->withBody(Utils::streamFor('<<BINARY BODY>>'));
        } else if ($request->getBody()->getSize() > self::RESPONSE_LIMIT) {
            $body = Str::limit($request->getBody()->getContents(), self::RESPONSE_LIMIT);
            $request = $request->withBody(Utils::streamFor($body));
        }

        // trim big response
        if ($response && $response->hasHeader('content-disposition') && Str::contains($response->getHeaderLine('content-disposition'), 'attachment')) {
            $response = $response->withBody(Utils::streamFor('<<BINARY BODY>>'));
        } else if ($response && $response->getBody()->getSize() > self::RESPONSE_LIMIT) {
            $body = Str::limit($response->getBody()->getContents(), self::RESPONSE_LIMIT);
            $response = $response->withBody(Utils::streamFor($body));
        }

        return str_replace(["\r\n", "\n"], ['\n', '\n'], (new MessageFormatter(self::TEMPLATE))->format($request, $response));
    }
}

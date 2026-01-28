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

    public function handle(ConnectionFailed|ResponseReceived $event)
    {
        if ($event instanceof ConnectionFailed) {
            Log::warning('[HttpClient] ConnectionFailed', ['http_client_request' => $this->format($event->request->toPsrRequest())]);

            return;
        }

        $context = [
            'http_client_request' => $this->format($event->request->toPsrRequest(), $event->response->toPsrResponse()),
            'http_client_request_duration' => $event->response->transferStats->getTransferTime(),
        ];

        $event->response->successful()
            ? Log::debug('[HttpClient] Success', $context)
            : Log::warning('[HttpClient] Error', $context);
    }

    private static function format(RequestInterface $request, ?ResponseInterface $response = null)
    {
        $limit = config('helpers.http_client_log_limit');

        // trim big request
        if ($request->hasHeader('content-type') && Str::contains($request->getHeaderLine('content-type'), 'multipart/form-data')) {
            $request = $request->withBody(Utils::streamFor('<<BINARY BODY>>'));
        } elseif ($request->getBody()->getSize() > $limit) {
            $body = Str::limit($request->getBody()->getContents(), $limit);
            $request = $request->withBody(Utils::streamFor($body));
        }

        // trim big response
        if ($response && $response->hasHeader('content-disposition') && Str::contains($response->getHeaderLine('content-disposition'), 'attachment')) {
            $response = $response->withBody(Utils::streamFor('<<BINARY BODY>>'));
        } elseif ($response && $response->getBody()->getSize() > $limit) {
            $body = Str::limit($response->getBody()->getContents(), $limit);
            $response = $response->withBody(Utils::streamFor($body));
        }

        return str_replace(["\r\n", "\n"], ['\n', '\n'], (new MessageFormatter(self::TEMPLATE))->format($request, $response));
    }
}

<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Cloud\VideoParser;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Hyperf\Codec\Json;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class HttpClient
{
    public function __construct(public array $config = [], public ?LoggerInterface $logger = null)
    {
        $this->config = array_merge(
            [
                'timeout' => 10,
            ],
            $this->config
        );
    }

    public function client(): Client
    {
        $stack = HandlerStack::create();
        $stack->push($this->getMiddleware(), 'retry');

        return new Client([
            'handler' => $stack,
            ...$this->config,
        ]);
    }

    public function getMiddleware(): callable
    {
        return Middleware::retry(function ($retries, RequestInterface $request, ?ResponseInterface $response = null) {
            if (! $this->isOk($response) && $retries < 5) {
                $this->logger?->warning(Json::encode([
                    'key' => 'cloud_video_parser_http_execute_try_again',
                    'uri' => (string) $request->getUri(),
                    'retries' => $retries,
                ]));
                return true;
            }
            return false;
        }, function () {
            return 100;
        });
    }

    /**
     * Check the response status is correct.
     */
    protected function isOk(?ResponseInterface $response): bool
    {
        return $response && $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }
}

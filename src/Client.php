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

use Cloud\VideoParser\Cloud\Qiniu;
use Cloud\VideoParser\Schema\Info;
use Hyperf\HttpMessage\Uri\Uri;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use RuntimeException;

class Client
{
    /**
     * @var array<string, CloudInterface>
     */
    public array $clouds = [];

    public function __construct(
        array $clouds = [],
        public ?LoggerInterface $logger = null,
    ) {
        $clouds = array_merge(
            [
                'qiniu' => new Qiniu($this->logger),
            ],
            $clouds
        );

        foreach ($clouds as $name => $cloud) {
            $client = match (true) {
                is_object($cloud) => $cloud,
                is_string($cloud) => new $cloud($this->logger),
                is_callable($cloud) => $cloud($this->logger),
                default => throw new InvalidArgumentException("Unsupported cloud provider: {$name}"),
            };

            if (! $client instanceof CloudInterface) {
                throw new RuntimeException(sprintf('%s must implement CloudInterface', $name));
            }

            $this->clouds[$name] = $client;
        }
    }

    public function info(string $url, string $cloud): Info
    {
        $client = $this->clouds[$cloud] ?? null;
        if (! $client) {
            throw new RuntimeException(sprintf('%s not found', $cloud));
        }

        return $client->info(new Uri($url));
    }

    public function coverUrl(string $url, string $cloud): string
    {
        $client = $this->clouds[$cloud] ?? null;
        if (! $client) {
            throw new RuntimeException(sprintf('%s not found', $cloud));
        }

        return $client->coverUrl(new Uri($url));
    }
}

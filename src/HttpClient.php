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

class HttpClient
{
    public function __construct(public array $config = [])
    {
        $this->config = array_merge(
            [
                'timeout' => 30,
            ],
            $this->config
        );
    }

    public function client(): Client
    {
        return new Client($this->config);
    }
}

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

namespace Cloud\VideoParser\Cloud;

use Cloud\VideoParser\CloudInterface;
use Cloud\VideoParser\Exception\InvalidVideoException;
use Cloud\VideoParser\Schema\Info;
use GuzzleHttp\Client;
use Hyperf\Codec\Json;
use Hyperf\HttpMessage\Uri\Uri;
use Psr\Log\LoggerInterface;

class Qiniu implements CloudInterface
{
    protected ?LoggerInterface $logger = null;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function info(Uri $uri): Info
    {
        $uri = $uri->withQuery('avinfo');

        $body = (string) (new Client())->get((string) $uri)->getBody();

        $result = Json::decode($body);
        if (empty($result['streams'][0])) {
            $this->logger?->error(Json::encode(['id' => 'info_error', 'url' => (string) $uri, 'exception' => $result]));
            throw new InvalidVideoException();
        }

        $stream = $result['streams'][0];
        $rFrameRate = explode('/', $stream['r_frame_rate']);

        return new Info(
            $stream['width'],
            $stream['height'],
            (int) round((float) bcdiv($rFrameRate[0], $rFrameRate[1], 2)),
            $stream['duration'],
            $result
        );
    }
}

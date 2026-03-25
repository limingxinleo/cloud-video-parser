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
use Cloud\VideoParser\CoverUrlBuilder;
use Cloud\VideoParser\HttpClient;
use Cloud\VideoParser\Schema\Info;
use Hyperf\Codec\Json;
use Hyperf\HttpMessage\Uri\Uri;
use Psr\Log\LoggerInterface;
use Throwable;

class Qiniu implements CloudInterface
{
    protected HttpClient $httpClient;

    public function __construct(protected ?LoggerInterface $logger = null, ?HttpClient $httpClient = null)
    {
        $this->httpClient = $httpClient ?? new HttpClient();
    }

    public function info(Uri $uri): Info
    {
        $uri = $uri->withQuery('avinfo');

        try {
            $body = (string) $this->httpClient->client()->get((string) $uri)->getBody();
        } catch (Throwable $exception) {
            $this->logger?->error(Json::encode(['id' => 'info_error', 'url' => (string) $uri, 'exception' => $exception]));
            throw $exception;
        }

        $res = Json::decode($body);

        $width = $res['streams'][0]['width'] ?? ($res['streams'][1]['width'] ?? 0); // 七牛云的视频元信息顺序不定
        $height = $res['streams'][0]['height'] ?? ($res['streams'][1]['height'] ?? 0);
        $nbFrames = $res['streams'][0]['nb_frames'] ?? ($res['streams'][1]['nb_frames'] ?? 0);
        $size = $res['format']['size'] ?? 0;
        $duration = $res['format']['duration'] ?? 0;

        return new Info(
            (int) $width,
            (int) $height,
            (int) $size,
            (int) ($nbFrames / $duration),
            (string) $duration,
            $res
        );
    }

    public function coverUrl(Uri $uri, ?CoverUrlBuilder $builder = null): string
    {
        $query = 'vframe/jpg/offset/0';
        if ($builder?->width !== null) {
            $query .= '/w/' . $builder->width;
        }

        if ($builder?->height !== null) {
            $query .= '/h/' . $builder->height;
        }

        return (string) $uri->withQuery($query);
    }
}

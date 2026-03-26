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
use Hyperf\Codec\Xml;
use Hyperf\HttpMessage\Uri\Uri;
use Psr\Log\LoggerInterface;
use Throwable;

class Tencent implements CloudInterface
{
    protected HttpClient $httpClient;

    public function __construct(protected ?LoggerInterface $logger = null, ?HttpClient $httpClient = null)
    {
        $this->httpClient = $httpClient ?? new HttpClient();
    }

    public function info(Uri $uri): Info
    {
        $uri = $uri->withQuery('ci-process=videoinfo');

        try {
            $body = (string) $this->httpClient->client()->get((string) $uri)->getBody();
        } catch (Throwable $exception) {
            $this->logger?->error(Json::encode(['id' => 'info_error', 'url' => (string) $uri, 'exception' => $exception]));
            throw $exception;
        }

        $res = Xml::toArray($body);

        $width = $res['Stream']['Video']['Width'] ?? 0; // 七牛云的视频元信息顺序不定
        $height = $res['Stream']['Video']['Height'] ?? 0;
        $fps = ($res['Stream']['Video']['Fps'] ?? $res['Stream']['Video']['AvgFps'] ?? 0);
        $size = $res['MediaInfo']['Format']['Size'] ?? 0;
        $duration = $res['MediaInfo']['Format']['Duration'] ?? $res['Stream']['Video']['Duration'] ?? 0;

        return new Info(
            (int) $width,
            (int) $height,
            (int) $size,
            (int) $fps,
            (string) $duration,
            $res
        );
    }

    public function coverUrl(Uri $uri, ?CoverUrlBuilder $builder = null): string
    {
        $query = 'ci-process=snapshot&time=0';
        if ($builder?->width !== null) {
            $query .= '&width=' . $builder->width;
        }

        if ($builder?->height !== null) {
            $query .= '&height=' . $builder->height;
        }

        return (string) $uri->withQuery($query);
    }
}

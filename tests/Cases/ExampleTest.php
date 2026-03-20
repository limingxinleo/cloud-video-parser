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

namespace HyperfTest\Cases;

use Cloud\VideoParser\Client;
use Cloud\VideoParser\CoverUrlBuilder;
use Hyperf\Codec\Json;

/**
 * @internal
 * @coversNothing
 */
class ExampleTest extends AbstractTestCase
{
    public function testInfo()
    {
        $samples = Json::decode(file_get_contents(__DIR__ . '/../../samples.json'));

        $client = new Client();

        foreach ($samples as $sample) {
            $name = $sample['name'];
            $url = $sample['url'];

            $res = $client->info($url, $name);

            $this->assertNotEmpty($res->width);
            $this->assertNotEmpty($res->height);
        }
    }

    public function testCoverUrl()
    {
        $samples = Json::decode(file_get_contents(__DIR__ . '/../../samples.json'));

        $client = new Client();

        foreach ($samples as $sample) {
            $name = $sample['name'];
            $url = $sample['url'];

            $res = $client->coverUrl($url, $name);

            (new \GuzzleHttp\Client())->get($res, ['sink' => $path = uniqid() . '.jpg']);

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $path);
            finfo_close($finfo);

            $this->assertTrue(str_starts_with($mimeType, 'image/'));
        }
    }

    public function testCoverUrlBuilder()
    {
        $client = new Client();

        $res = $client->coverUrl('https://xxxx.xxx.com', 'qiniu', CoverUrlBuilder::create()->resize(100, 100));

        $this->assertSame('https://xxxx.xxx.com?vframe/jpg/offset/0/w/100/h/100', $res);
    }
}

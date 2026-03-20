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
use Hyperf\Codec\Json;

/**
 * @internal
 * @coversNothing
 */
class ExampleTest extends AbstractTestCase
{
    public function testGetInfo()
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
}

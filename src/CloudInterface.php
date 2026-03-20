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

use Cloud\VideoParser\Schema\Info;
use Hyperf\HttpMessage\Uri\Uri;
use Psr\Log\LoggerInterface;

interface CloudInterface
{
    public function __construct(?LoggerInterface $logger = null);

    public function info(Uri $uri): Info;

    public function coverUrl(Uri $uri): string;
}

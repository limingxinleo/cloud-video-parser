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

namespace Cloud\VideoParser\Schema;

class Info
{
    public function __construct(
        public int $width,
        public int $height,
        public int $fps,
        public string $duration,
        public array $rawData,
    ) {
    }
}

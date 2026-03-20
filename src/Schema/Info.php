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

use JsonSerializable;

class Info implements JsonSerializable
{
    public function __construct(
        public int $width,
        public int $height,
        public int $size,
        public int $fps,
        public string $duration,
        public array $rawData,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'width' => $this->width,
            'height' => $this->height,
            'size' => $this->size,
            'fps' => $this->fps,
            'duration' => $this->duration,
        ];
    }
}

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

class CoverUrlBuilder
{
    public ?int $width = null;

    public ?int $height = null;

    public static function create(): static
    {
        return new static();
    }

    public function resize(?int $width = null, ?int $height = null): static
    {
        $this->width = $width;
        $this->height = $height;

        return $this;
    }
}

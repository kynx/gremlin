<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

final readonly class ByteType extends AbstractIntegerType
{
    public static function getSize(): int
    {
        return 1;
    }

    public static function getMin(): int
    {
        return 0;
    }

    public static function getMax(): int
    {
        return 255;
    }
}

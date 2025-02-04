<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

final readonly class ShortType extends AbstractIntegerType
{
    public static function getSize(): int
    {
        return 2;
    }

    public static function getMin(): int
    {
        return -32767;
    }

    public static function getMax(): int
    {
        return 32767;
    }
}

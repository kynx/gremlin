<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

final readonly class IntType extends AbstractIntegerType
{
    public static function getSize(): int
    {
        return 4;
    }

    public static function getMin(): int
    {
        return -2147483647;
    }

    public static function getMax(): int
    {
        return 2147483647;
    }
}

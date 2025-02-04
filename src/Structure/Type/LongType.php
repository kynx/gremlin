<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

use Kynx\Gremlin\Structure\Type\AbstractIntegerType;

final readonly class LongType extends AbstractIntegerType
{
    public static function getSize(): int
    {
        return 64;
    }

    public static function getMin(): int
    {
        return -9223372036854775807;
    }

    public static function getMax(): int
    {
        return 9223372036854775807;
    }
}

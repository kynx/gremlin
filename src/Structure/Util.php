<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure;

use Stringable;

use function get_debug_type;
use function is_scalar;
use function strlen;
use function substr;

final readonly class Util
{
    public static function toString(mixed $value): string
    {
        return match (true) {
            $value === null                                 => '`null`',
            $value === true                                 => '`true`',
            $value === false                                => '`false`',
            is_scalar($value), $value instanceof Stringable => self::truncate((string) $value),
            default                                         => get_debug_type($value),
        };
    }

    private static function truncate(string $value): string
    {
        return strlen($value) > 20 ? substr($value, 0, 17) . '...' : $value;
    }
}

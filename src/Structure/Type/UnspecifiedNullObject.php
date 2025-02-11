<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

final readonly class UnspecifiedNullObject implements TypeInterface
{
    public function getValue(): null
    {
        return null;
    }

    public function equals(mixed $other): bool
    {
        return $other instanceof UnspecifiedNullObject;
    }

    public function __toString(): string
    {
        return self::NULL_STRING;
    }
}

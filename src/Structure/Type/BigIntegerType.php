<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

use Brick\Math\BigInteger;

final readonly class BigIntegerType implements TypeInterface
{
    public function __construct(private ?BigInteger $value)
    {
    }

    public function getValue(): ?BigInteger
    {
        return $this->value;
    }

    public function equals(mixed $other): bool
    {
        if (! $other instanceof BigIntegerType) {
            return false;
        }

        if ($other->value === null) {
            return $this->value === null;
        }

        if ($this->value === null) {
            return $other->value === null;
        }

        return $this->value->isEqualTo($other->value);
    }

    public function __toString(): string
    {
        if ($this->value === null) {
            return self::NULL_STRING;
        }

        return (string) $this->value;
    }
}

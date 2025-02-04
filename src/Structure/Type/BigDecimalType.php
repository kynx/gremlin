<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

use Brick\Math\BigDecimal as BrickBigDecimal;

final readonly class BigDecimalType implements TypeInterface
{
    public function __construct(private ?BrickBigDecimal $value)
    {
    }

    public function getValue(): ?BrickBigDecimal
    {
        return $this->value;
    }

    public function equals(mixed $other): bool
    {
        if (! $other instanceof BigDecimalType) {
            return false;
        }

        if ($this->value === null) {
            return $other->value === null;
        }

        if ($other->value === null) {
            return $this->value === null;
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

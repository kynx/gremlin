<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

abstract readonly class AbstractIntegerType implements TypeInterface
{
    public function __construct(private ?int $value)
    {
        if ($this->value === null) {
            return;
        }

        if (static::getMax() >= PHP_INT_MAX || static::getMin() <= PHP_INT_MIN) {
            return;
        }

        if ($this->value > static::getMax() || $this->value < static::getMin()) {
            throw TypeException::intOutOfRange(static::getMin(), static::getMax(), $this->value);
        }
    }

    abstract public static function getSize(): int;

    abstract public static function getMin(): int;

    abstract public static function getMax(): int;

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function equals(mixed $other): bool
    {
        if (! $other instanceof AbstractIntegerType) {
            return false;
        }

        return $this::getSize() === $other::getSize() && $this->value === $other->value;
    }

    public function __toString(): string
    {
        if ($this->value === null) {
            return self::NULL_STRING;
        }

        return (string) $this->value;
    }
}

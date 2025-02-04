<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

final readonly class DoubleType implements TypeInterface
{
    public function __construct(private ?float $value)
    {
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function equals(mixed $other): bool
    {
        if (! $other instanceof DoubleType) {
            return false;
        }

        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        if ($this->value === null) {
            return self::NULL_STRING;
        }

        return (string) $this->value;
    }
}

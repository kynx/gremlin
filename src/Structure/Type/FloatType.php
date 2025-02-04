<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

final readonly class FloatType implements TypeInterface
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
        if (! $other instanceof FloatType) {
            return false;
        }

        return $other->value === $this->value;
    }

    public function __toString(): string
    {
        if ($this->value === null) {
            return self::NULL_STRING;
        }

        return (string) $this->value;
    }
}

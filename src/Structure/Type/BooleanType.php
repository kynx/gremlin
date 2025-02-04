<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

final readonly class BooleanType implements TypeInterface
{
    public function __construct(private ?bool $value)
    {
    }

    public function getValue(): ?bool
    {
        return $this->value;
    }

    public function equals(mixed $other): bool
    {
        if (! $other instanceof BooleanType) {
            return false;
        }

        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        if ($this->value === null) {
            return self::NULL_STRING;
        }

        return $this->value ? 'true' : 'false';
    }
}

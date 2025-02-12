<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

final readonly class ClassType implements TypeInterface
{
    public function __construct(private ?string $value)
    {
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function equals(mixed $other): bool
    {
        if (! $other instanceof ClassType) {
            return false;
        }

        return $other->value === $this->value;
    }

    public function __toString(): string
    {
        if ($this->value === null) {
            return self::NULL_STRING;
        }

        return $this->value;
    }
}

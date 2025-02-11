<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

use DateTimeInterface;

final readonly class DateType implements TypeInterface
{
    public function __construct(private ?DateTimeInterface $value)
    {
    }

    public function getValue(): ?DateTimeInterface
    {
        return $this->value;
    }

    public function equals(mixed $other): bool
    {
        if (! $other instanceof DateType) {
            return false;
        }

        // phpcs:ignore SlevomatCodingStandard.Operators.DisallowEqualOperators.DisallowedEqualOperator
        return $other->value == $this->value;
    }

    public function __toString(): string
    {
        if ($this->value === null) {
            return self::NULL_STRING;
        }

        return $this->value->format(DateTimeInterface::RFC3339_EXTENDED);
    }
}

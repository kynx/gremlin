<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

use function preg_match;

final readonly class UuidType implements TypeInterface
{
    private const string FORMAT = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

    public function __construct(private ?string $value)
    {
        if ($this->value !== null && ! preg_match('/' . self::FORMAT . '/', $this->value)) {
            throw TypeException::invalidFormat(self::FORMAT, $this->value);
        }
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function equals(mixed $other): bool
    {
        // phpcs:ignore SlevomatCodingStandard.Operators.DisallowEqualOperators.DisallowedEqualOperator
        return $this == $other;
    }

    public function __toString(): string
    {
        if ($this->value === null) {
            return self::NULL_STRING;
        }

        return $this->value;
    }
}

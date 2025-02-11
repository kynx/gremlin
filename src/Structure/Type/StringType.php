<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

use function mb_detect_encoding;
use function substr;

final readonly class StringType implements TypeInterface
{
    /**
     * @throws TypeException
     */
    public function __construct(private ?string $value)
    {
        if ($this->value === null || $this->value === '') {
            return;
        }

        if (mb_detect_encoding(substr($this->value, 0, 1024), ['UTF-8'], true) !== 'UTF-8') {
            throw TypeException::invalidString($this->value);
        }
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function equals(mixed $other): bool
    {
        if (! $other instanceof StringType) {
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

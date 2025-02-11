<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

use function mb_detect_encoding;
use function mb_strlen;

final readonly class CharType implements TypeInterface
{
    /**
     * @throws TypeException
     */
    public function __construct(private ?string $value)
    {
        if ($this->value === null) {
            return;
        }

        if (mb_strlen($this->value, 'UTF-8') !== 1 || mb_detect_encoding($this->value, ['UTF-8'], true) !== 'UTF-8') {
            throw TypeException::invalidCharString($this->value);
        }
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function equals(mixed $other): bool
    {
        if (! $other instanceof CharType) {
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

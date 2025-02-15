<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

/**
 * @psalm-type ValuesType = iterable<array-key, TypeInterface>|null
 * @template-extends AbstractArrayType<ValuesType>
 */
final readonly class ListType extends AbstractArrayType
{
    public function __toString(): string
    {
        if ($this->values === null) {
            return self::NULL_STRING;
        }

        return "List($this->length)";
    }
}

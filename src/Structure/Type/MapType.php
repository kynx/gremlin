<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

/**
 * @psalm-type ValuesType = iterable<array-key, MapItem>|null
 * @template-extends AbstractArrayType<ValuesType>
 */
final readonly class MapType extends AbstractArrayType
{
    public function __toString(): string
    {
        if ($this->values === null) {
            return self::NULL_STRING;
        }

        return "Map($this->length)";
    }
}

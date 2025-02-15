<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

use Generator;
use Traversable;

use function array_pop;
use function array_reverse;
use function in_array;
use function iterator_to_array;

/**
 * @psalm-type ValuesType = iterable<array-key, TypeInterface>|null
 * @template-extends AbstractArrayType<ValuesType>
 */
final readonly class SetType extends AbstractArrayType
{
    /**
     * @param ValuesType|Generator $values
     */
    public function __construct(?iterable $values)
    {
        if ($values instanceof Generator) {
            throw TypeException::unsupportedValue($this, $values);
        }

        parent::__construct($values);

        if ($this->values === null) {
            return;
        }

        $values    = $this->values instanceof Traversable ? iterator_to_array($this->values) : $this->values;
        $remaining = array_reverse($values);

        while (($value = array_pop($remaining)) !== null) {
            if (in_array($value, $remaining)) {
                throw TypeException::duplicateValue($value);
            }
        }
    }

    public function __toString(): string
    {
        if ($this->values === null) {
            return self::NULL_STRING;
        }

        return "Set($this->length)";
    }
}

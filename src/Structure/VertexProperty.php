<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure;

use function sprintf;

final readonly class VertexProperty extends AbstractElement
{
    /** @var array<string, Property> */
    public array $properties;

    /**
     * @param array<Property> $properties
     */
    public function __construct(
        int $id,
        string $label,
        public mixed $value,
        array $properties
    ) {
        parent::__construct($id, $label);

        $keyed = [];
        foreach ($properties as $property) {
            $keyed[$property->key] = $property;
        }

        $this->properties = $keyed;
    }

    public function __toString(): string
    {
        return sprintf('vp[%s->%s]', $this->label, Util::toString($this->value));
    }
}

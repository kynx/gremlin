<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure;

use function sprintf;

final readonly class Vertex extends AbstractElement
{
    public const string DEFAULT_LABEL = 'vertex';

    /** @var array<string, VertexProperty> */
    public array $properties;

    /**
     * @param array<VertexProperty> $properties
     */
    public function __construct(
        int $id,
        array $properties = [],
        string $label = self::DEFAULT_LABEL
    ) {
        parent::__construct($id, $label);

        $keyed = [];
        foreach ($properties as $property) {
            $keyed[$property->label] = $property;
        }

        $this->properties = $keyed;
    }

    public function __toString(): string
    {
        return sprintf("v[%s]", $this->id);
    }
}

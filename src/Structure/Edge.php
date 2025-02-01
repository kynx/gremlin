<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure;

use function sprintf;

final readonly class Edge extends AbstractElement
{
    public const string DEFAULT_LABEL = 'edge';

    /** @var array<string, Property> */
    public array $properties;

    /**
     * @param array<Property> $properties
     */
    public function __construct(
        int $id,
        public ?Vertex $outV,
        public ?Vertex $inV,
        array $properties = [],
        string $label = self::DEFAULT_LABEL
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
        return sprintf(
            "e[%s][%s-%s-%s]",
            $this->id,
            Util::toString($this->outV),
            $this->label,
            Util::toString($this->inV)
        );
    }
}

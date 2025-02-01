<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure;

use Stringable;

use function array_map;
use function implode;
use function sprintf;

final readonly class Path implements Stringable
{
    /**
     * @param array<string> $labels
     * @param array<Stringable> $objects
     */
    public function __construct(private array $labels, private array $objects)
    {
    }

    public function equals(mixed $other): bool
    {
        return $other instanceof $this
            && $other->labels === $this->labels
            && $other->objects === $this->objects;
    }

    public function __toString(): string
    {
        $objects = array_map(static fn (Stringable $object): string => (string) $object, $this->objects);

        return sprintf('path[%s]', implode(', ', $objects));
    }
}

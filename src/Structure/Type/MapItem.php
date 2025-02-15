<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

/**
 * Graph maps can use any type as a key
 */
final readonly class MapItem
{
    public function __construct(public TypeInterface $key, public TypeInterface $value)
    {
    }
}

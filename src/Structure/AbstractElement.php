<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure;

use Stringable;

abstract readonly class AbstractElement implements Stringable
{
    public function __construct(public int $id, public string $label)
    {
    }

    public function equals(mixed $other): bool
    {
        return $other instanceof $this && $other->id === $this->id;
    }
}

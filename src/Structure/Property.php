<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure;

use Stringable;

use function sprintf;

final readonly class Property implements Stringable
{
    public function __construct(public string $key, public mixed $value)
    {
    }

    public function equals(mixed $other): bool
    {
        return $other instanceof $this
            && $other->key === $this->key
            && $other->value === $this->value;
    }

    public function __toString(): string
    {
        return sprintf('p[%s->%s]', $this->key, Util::toString($this->value));
    }
}

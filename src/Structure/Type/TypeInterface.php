<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

use Stringable;

/**
 * @immutable
 */
interface TypeInterface extends Stringable
{
    public const string NULL_STRING = 'null';

    public function getValue(): mixed;

    public function equals(mixed $other): bool;
}

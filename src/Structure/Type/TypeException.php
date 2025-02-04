<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

use DomainException;
use Kynx\Gremlin\ExceptionInterface;

use function sprintf;

final class TypeException extends DomainException implements ExceptionInterface
{
    public static function intOutOfRange(int $min, int $max, int $value): self
    {
        return new self(sprintf("Expected value between %s and %s, got %s", $min, $max, $value));
    }
}

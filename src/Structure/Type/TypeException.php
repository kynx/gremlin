<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

use DomainException;
use Kynx\Gremlin\ExceptionInterface;

use function get_debug_type;
use function sprintf;

final class TypeException extends DomainException implements ExceptionInterface
{
    public static function intOutOfRange(int $min, int $max, int $value): self
    {
        return new self(sprintf("Expected value between %s and %s, got %s", $min, $max, $value));
    }

    public static function invalidCharString(string $value): self
    {
        return new self("Expected single character UTF-8 string, got: '$value'");
    }

    public static function invalidString(string $value): self
    {
        return new self("Expected UTF-8 string, got: '$value'");
    }

    public static function invalidType(string $expected, mixed $value): self
    {
        return new self(sprintf("Expected value of type '%s', got: %s", $expected, get_debug_type($value)));
    }

    public static function unknownBufferLength(): self
    {
        return new self("Cannot calculate buffer length");
    }
}

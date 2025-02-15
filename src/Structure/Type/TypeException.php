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

    public static function unsupportedValue(TypeInterface $type, mixed $value): self
    {
        return new self(sprintf("%s does not accept values of type '%s'", $type::class, get_debug_type($value)));
    }

    public static function invalidFormat(string $expected, string $value): self
    {
        return new self(sprintf("Expected string in format '%s', got: '%s'", $expected, $value));
    }

    public static function unknownBufferLength(): self
    {
        return new self("Cannot calculate buffer length");
    }

    public static function unknownLength(TypeInterface $type, mixed $value): self
    {
        return new self(sprintf(
            "Length is required for type %s with value of: %s",
            $type::class,
            get_debug_type($value)
        ));
    }

    public static function duplicateValue(TypeInterface $value): self
    {
        return new self(sprintf("Duplicate value '%s' found in set", $value));
    }
}

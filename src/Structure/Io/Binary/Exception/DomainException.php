<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Exception;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Io\IoExceptionInterface;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Throwable;

use function dechex;
use function get_debug_type;
use function sprintf;

final class DomainException extends \DomainException implements IoExceptionInterface
{
    private function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function unknownBinaryType(int $type, ?Throwable $previous): self
    {
        return new self(sprintf('Unknown binary type 0x%02s', dechex($type)), 0, $previous);
    }

    public static function unsupportedBinaryType(BinaryType $type): self
    {
        return new self(sprintf('No serializer found for type 0x%02s', dechex($type->value)));
    }

    public static function unsupportedPhpType(TypeInterface $type): self
    {
        return new self(sprintf("No serializer found for type '%s'", $type::class));
    }

    public static function invalidType(SerializerInterface $serializer, mixed $value): self
    {
        return new self(sprintf(
            "Expected value of type '%s', got '%s'",
            $serializer->getPhpType(),
            get_debug_type($value)
        ));
    }

    public static function ofThrowable(Throwable $throwable): self
    {
        return new self($throwable->getMessage(), 0, $throwable);
    }
}

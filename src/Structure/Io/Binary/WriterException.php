<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary;

use InvalidArgumentException;
use Kynx\Gremlin\ExceptionInterface;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Throwable;

use function get_debug_type;
use function sprintf;

final class WriterException extends InvalidArgumentException implements ExceptionInterface
{
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

    public static function invalidPackLength(int $length): self
    {
        return new self(sprintf("Invalid integer length: %s", $length));
    }

    public static function fromThrowable(Throwable $throwable): self
    {
        return new self($throwable->getMessage(), 0, $throwable);
    }
}

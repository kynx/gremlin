<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary;

use DomainException;
use Kynx\Gremlin\ExceptionInterface;
use Throwable;

use function dechex;
use function sprintf;

final class ReaderException extends DomainException implements ExceptionInterface
{
    public static function unsupportedBinaryType(int $type): self
    {
        return new self(sprintf("No serializer found for GraphBinary type '0x%s'", dechex($type)));
    }

    public static function fromThrowable(Throwable $throwable): self
    {
        return new self($throwable->getMessage(), 0, $throwable);
    }
}

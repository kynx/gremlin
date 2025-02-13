<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Exception;

use Kynx\Gremlin\Structure\Io\IoExceptionInterface;
use RuntimeException;
use Throwable;

final class StreamException extends RuntimeException implements IoExceptionInterface
{
    private function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function unreadableStream(?Throwable $previous): self
    {
        return new self("Error reading stream", 0, $previous);
    }

    public static function unwritableStream(?Throwable $previous): self
    {
        return new self("Error writing stream", 0, $previous);
    }
}

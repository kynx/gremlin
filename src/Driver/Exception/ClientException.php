<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Driver\Exception;

use RuntimeException;
use Throwable;

final class ClientException extends RuntimeException implements TransportExceptionInterface
{
    private function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromResponse(int $code, string $reason): self
    {
        return new self("Server responded with '$reason'", $code);
    }

    public static function fromThrowable(Throwable $throwable): self
    {
        return new self($throwable->getMessage(), 0, $throwable);
    }
}

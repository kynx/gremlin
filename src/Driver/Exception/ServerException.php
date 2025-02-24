<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Driver\Exception;

use RuntimeException;
use Throwable;

use function sprintf;

final class ServerException extends RuntimeException implements TransportExceptionInterface
{
    private function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromResponse(int $code, string $reason): self
    {
        return new self("Server responded with '$reason'", $code);
    }

    public static function unexpectedResponse(int $code, string $reason): self
    {
        return new self(sprintf("Unexpected response from server: %s %s", $code, $reason), $code);
    }
}

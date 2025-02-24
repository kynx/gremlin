<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Driver\Exception;

use RuntimeException;
use Throwable;

use function sprintf;

final class IdentityException extends RuntimeException implements TransportExceptionInterface
{
    private function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function unauthenticated(string $reason): self
    {
        return new self(sprintf("Authentication failed: %s", $reason), 401);
    }

    public static function unauthorised(string $reason): self
    {
        return new self(sprintf("Not authorized: %s", $reason), 403);
    }
}

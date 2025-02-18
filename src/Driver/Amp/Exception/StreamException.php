<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Driver\Amp\Exception;

use Kynx\Gremlin\Driver\DriverExceptionInterface;
use RuntimeException;

final class StreamException extends RuntimeException implements DriverExceptionInterface
{
    public static function cannotTell(): self
    {
        return new self("Cannot tell() buffer stream");
    }

    public static function cannotSeek(): self
    {
        return new self("Cannot seek() buffer stream");
    }

    public static function cannotRewind(): self
    {
        return new self("Cannot rewind() buffer stream");
    }

    public static function cannotWrite(): self
    {
        return new self("Cannot write() read-only stream");
    }
}
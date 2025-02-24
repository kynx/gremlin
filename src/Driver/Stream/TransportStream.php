<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Driver\Stream;

use Closure;
use Psr\Http\Message\StreamInterface;
use Throwable;

use function sprintf;
use function trigger_error;

use const E_USER_WARNING;
use const SEEK_SET;

/**
 * A delegating stream with an `$onClose` callback
 *
 * The callback gives transport providers the opportunity to close the underlying connection when the stream is
 * closed or destroyed. If the callback throws an exception during `TransportStream::__destruct()` an `E_USER_WARNING`
 * will be triggered. To avoid this, code consuming this stream should always explicitly close it once read.
 */
final class TransportStream implements StreamInterface
{
    public function __construct(private readonly StreamInterface $delegate, private ?Closure $onClose)
    {
    }

    public function close(): void
    {
        if ($this->onClose !== null) {
            ($this->onClose)();
            $this->onClose = null;
        }
        $this->delegate->close();
    }

    /**
     * @return null|resource
     */
    public function detach()
    {
        return $this->delegate->detach();
    }

    public function getSize(): ?int
    {
        return $this->delegate->getSize();
    }

    public function tell(): int
    {
        return $this->delegate->tell();
    }

    public function eof(): bool
    {
        return $this->delegate->eof();
    }

    public function isSeekable(): bool
    {
        return $this->delegate->isSeekable();
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        $this->delegate->seek($offset, $whence);
    }

    public function rewind(): void
    {
        $this->delegate->rewind();
    }

    public function isWritable(): bool
    {
        return $this->delegate->isWritable();
    }

    public function write(string $string): int
    {
        return $this->delegate->write($string);
    }

    public function isReadable(): bool
    {
        return $this->delegate->isReadable();
    }

    public function read(int $length): string
    {
        return $this->delegate->read($length);
    }

    public function getContents(): string
    {
        return $this->delegate->getContents();
    }

    public function getMetadata(?string $key = null): mixed
    {
        return $this->delegate->getMetadata($key);
    }

    public function __toString(): string
    {
        return $this->delegate->__toString();
    }

    public function __destruct()
    {
        if ($this->onClose === null) {
            return;
        }

        try {
            ($this->onClose)();
        } catch (Throwable $throwable) {
            trigger_error(sprintf(
                "Error destroying %s: %s\n%s",
                self::class,
                $throwable->getMessage(),
                $throwable->getTraceAsString()
            ), E_USER_WARNING);
        }
    }
}

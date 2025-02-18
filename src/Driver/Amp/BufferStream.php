<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Driver\Amp;

use Amp\Websocket\WebsocketMessage;
use Kynx\Gremlin\Driver\Amp\Exception\StreamException;
use Psr\Http\Message\StreamInterface;

use function strlen;
use function substr;

use const SEEK_SET;

final class BufferStream implements StreamInterface
{
    private string $buffer;

    public function __construct(WebsocketMessage $message)
    {
        $this->buffer = $message->buffer();
        $message->close();
    }

    public function close(): void
    {
        $this->buffer = '';
    }

    public function detach(): null
    {
        $this->close();
        return null;
    }

    public function getSize(): ?int
    {
        return null;
    }

    public function tell(): int
    {
        throw StreamException::cannotTell();
    }

    public function eof(): bool
    {
        return $this->buffer === '';
    }

    public function isSeekable(): bool
    {
        return false;
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        throw StreamException::cannotSeek();
    }

    public function rewind(): void
    {
        throw StreamException::cannotRewind();
    }

    public function isWritable(): bool
    {
        return false;
    }

    public function write(string $string): int
    {
        throw StreamException::cannotWrite();
    }

    public function isReadable(): bool
    {
        return true;
    }

    public function read(int $length): string
    {
        if ($length >= strlen($this->buffer)) {
            try {
                return $this->buffer;
            } finally {
                $this->buffer = '';
            }
        }

        try {
            return substr($this->buffer, 0, $length);
        } finally {
            $this->buffer = substr($this->buffer, $length);
        }
    }

    public function getContents(): string
    {
        try {
            return $this->buffer;
        } finally {
            $this->buffer = '';
        }
    }

    public function getMetadata(?string $key = null): array|null
    {
        return $key === null ? [] : null;
    }

    public function __toString(): string
    {
        return $this->getContents();
    }
}

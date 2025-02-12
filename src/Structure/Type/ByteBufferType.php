<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

use Generator;

use function assert;
use function count;
use function fclose;
use function feof;
use function fopen;
use function fread;
use function fstat;
use function fwrite;
use function is_int;
use function is_resource;
use function pack;
use function rewind;
use function stream_get_contents;
use function strlen;
use function unpack;

/**
 * @todo This might use a lot of file handles if many small buffers are created 🤔
 */
final class ByteBufferType implements ByteBufferTypeInterface
{
    /** @var resource|closed-resource */
    private $resource;

    /**
     * @param resource $resource
     */
    private function __construct($resource, private int $length)
    {
        $this->resource = $resource;
    }

    /**
     * @inheritDoc
     */
    public static function ofResource($resource, ?int $length = null): self
    {
        /** @psalm-suppress DocblockTypeContradiction We want to be sure... */
        if (! is_resource($resource)) {
            throw TypeException::invalidType('resource', $resource);
        }

        if ($length === null) {
            $stat   = (array) fstat($resource);
            $length = $stat['size'] ?? null;
        }
        if ($length === null) {
            throw TypeException::unknownBufferLength();
        }

        return new self($resource, $length);
    }

    public static function ofString(string $string): self
    {
        $resource = self::getResource();
        fwrite($resource, $string);
        rewind($resource);

        return new self($resource, strlen($string));
    }

    public static function ofByteArray(array $bytes): self
    {
        $length   = count($bytes);
        $resource = self::getResource();

        foreach ($bytes as $byte) {
            /** @psalm-suppress DocblockTypeContradiction We want to be sure... */
            if (! is_int($byte)) {
                throw TypeException::invalidType('integer', $byte);
            }
            /** @psalm-suppress TypeDoesNotContainType We want to be sure... */
            if ($byte < 0 || $byte > 255) {
                /** @psalm-suppress NoValue */
                throw TypeException::intOutOfRange(0, 255, $byte);
            }

            fwrite($resource, pack('C', $byte));
        }
        rewind($resource);

        return new self($resource, $length);
    }

    public function getValue(): string
    {
        if (! is_resource($this->resource)) {
            return '';
        }

        try {
            return (string) stream_get_contents($this->resource);
        } finally {
            fclose($this->resource);
        }
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function read(int $length = 1): string
    {
        if (! is_resource($this->resource)) {
            return '';
        }

        try {
            return (string) fread($this->resource, $length);
        } finally {
            if (feof($this->resource)) {
                @fclose($this->resource);
            }
        }
    }

    public function eof(): bool
    {
        return ! is_resource($this->resource) || feof($this->resource);
    }

    /**
     * @return Generator<int, int<0, 255>, mixed, void>
     */
    public function getIterator(): Generator
    {
        if (! is_resource($this->resource)) {
            return;
        }

        while (! feof($this->resource)) {
            $read = (string) fread($this->resource, 8092);
            if ($read === '') {
                break;
            }

            $len      = strlen($read);
            $unpacked = unpack("C$len", $read);
            if ($unpacked === false) {
                break;
            }

            /** @var int $int */
            foreach ($unpacked as $int) {
                assert(0 <= $int && $int < 256);
                yield $int;
            }
        }

        fclose($this->resource);
    }

    public function getByteArray(): array
    {
        $bytes = [];
        foreach ($this->getIterator() as $byte) {
            assert(0 <= $byte && $byte < 256);
            $bytes[] = $byte;
        }

        return $bytes;
    }

    public function equals(mixed $other): bool
    {
        return false;
    }

    public function __toString(): string
    {
        return "[$this->length byte buffer]";
    }

    public function __destruct()
    {
        if (is_resource($this->resource)) {
            @fclose($this->resource);
        }
    }

    /**
     * @return resource
     */
    private static function getResource()
    {
        $resource = fopen('php://memory', 'w+');
        assert(is_resource($resource));

        return $resource;
    }
}

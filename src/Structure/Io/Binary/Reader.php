<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary;

use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Exception\StreamException;
use Kynx\Gremlin\Structure\Io\Binary\Exception\UnderflowException;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

use function assert;
use function bin2hex;
use function hexdec;
use function is_array;
use function ord;
use function strlen;
use function unpack;

final readonly class Reader
{
    /** @var array<int, SerializerInterface> */
    private array $serializers;

    public function __construct(SerializerInterface ...$serializers)
    {
        $keyed = [];
        foreach ($serializers as $serializer) {
            $keyed[$serializer->getBinaryType()->value] = $serializer;
        }

        $this->serializers = $keyed;
    }

    public function read(StreamInterface $stream): TypeInterface
    {
        $type = BinaryType::fromChr($this->readBytes($stream, 1));
        if (! isset($this->serializers[$type->value])) {
            throw DomainException::unsupportedBinaryType($type);
        }

        return $this->serializers[$type->value]->unserialize($stream, $this);
    }

    public function isNull(StreamInterface $stream): bool
    {
        return $this->readBytes($stream, 1) === BinaryType::FLAG_NULL;
    }

    public function readByte(StreamInterface $stream): int
    {
        return ord($this->readBytes($stream, 1));
    }

    public function readShort(StreamInterface $stream): int
    {
        return $this->unpackInt($this->readBytes($stream, 2));
    }

    public function readInt(StreamInterface $stream): int
    {
        return $this->unpackInt($this->readBytes($stream, 4));
    }

    public function readUInt(StreamInterface $stream): int
    {
        return (int) $this->unpack('N', $this->readBytes($stream, 4));
    }

    public function readLong(StreamInterface $stream): int
    {
        return $this->unpackInt($this->readBytes($stream, 8));
    }

    public function readFloat(StreamInterface $stream): float
    {
        return (float) $this->unpack('G', $this->readBytes($stream, 4));
    }

    public function readDouble(StreamInterface $stream): float
    {
        return (float) $this->unpack('E', $this->readBytes($stream, 8));
    }

    public function readBytes(StreamInterface $stream, int $length): string
    {
        if ($length === 0) {
            return '';
        }

        try {
            $bytes = $stream->read($length);
        } catch (RuntimeException $exception) {
            throw StreamException::unreadableStream($exception);
        }

        if ($bytes === '') {
            throw UnderflowException::emptyStream();
        }
        if (strlen($bytes) !== $length) {
            throw UnderflowException::dataNotRead($length, strlen($bytes));
        }

        return $bytes;
    }

    private function unpack(string $format, string $binary): float|int
    {
        /** @var array<1, float|int>|false $unpacked */
        $unpacked = unpack($format, $binary);
        assert(is_array($unpacked));

        return $unpacked[1];
    }

    private function unpackInt(string $binary): int
    {
        if ($twosCompliment = ord($binary[0]) >= 0x80) {
            $binary = ~$binary;
        }

        return $twosCompliment ? 0 - ((int) hexdec(bin2hex($binary)) + 1) : (int) hexdec(bin2hex($binary));
    }
}

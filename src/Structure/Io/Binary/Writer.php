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

use function chr;
use function dechex;
use function hex2bin;
use function pack;
use function str_pad;
use function substr;

use const STR_PAD_LEFT;

final readonly class Writer
{
    /** @var array<class-string<TypeInterface>, SerializerInterface> */
    private array $serializers;

    public function __construct(SerializerInterface ...$serializers)
    {
        $keyed = [];

        foreach ($serializers as $serializer) {
            $keyed[$serializer->getPhpType()] = $serializer;
        }

        $this->serializers = $keyed;
    }

    public function write(StreamInterface $stream, TypeInterface $type): void
    {
        if (! isset($this->serializers[$type::class])) {
            throw DomainException::unsupportedPhpType($type);
        }

        $writer = $this->serializers[$type::class];
        $this->writeBytes($stream, $writer->getBinaryType()->toChr(), 1);
        $writer->serialize($stream, $type, $this);
    }

    public function writeNull(StreamInterface $stream): void
    {
        $this->writeBytes($stream, BinaryType::FLAG_NULL, 1);
    }

    public function writeNotNull(StreamInterface $stream): void
    {
        $this->writeBytes($stream, BinaryType::FLAG_NONE, 1);
    }

    public function writeByte(StreamInterface $stream, int $byte): void
    {
        $this->writeBytes($stream, chr($byte), 1);
    }

    public function writeShort(StreamInterface $stream, int $short): void
    {
        $this->writeBytes($stream, $this->pack('n', $short, 2), 2);
    }

    public function writeInt(StreamInterface $stream, int $int): void
    {
        $this->writeBytes($stream, $this->packInt($int, 4), 4);
    }

    public function writeUInt(StreamInterface $stream, int $int): void
    {
        $this->writeBytes($stream, $this->pack('N', $int, 4), 4);
    }

    public function writeLong(StreamInterface $stream, int $long): void
    {
        $this->writeBytes($stream, $this->packInt($long, 8), 8);
    }

    public function writeFloat(StreamInterface $stream, float $float): void
    {
        $this->writeBytes($stream, pack('G', $float), 4);
    }

    public function writeDouble(StreamInterface $stream, float $double): void
    {
        $this->writeBytes($stream, pack('E', $double), 8);
    }

    public function writeBytes(StreamInterface $stream, string $bytes, int $length): void
    {
        try {
            $written = $stream->write($bytes);
        } catch (RuntimeException $exception) {
            throw StreamException::unwritableStream($exception);
        }

        if ($length !== $written) {
            throw UnderflowException::dataNotWritten($length, $written);
        }
    }

    private function pack(string $format, int $int, int $size): string
    {
        return substr(pack($format, $int), 0 - $size);
    }

    public function packInt(int $int, int $size): string
    {
        // 2 hexadecimal digits for each byte
        $length = $size * 2;

        return (string) hex2bin(substr(
            str_pad(dechex($int), $length, $int < 0 ? 'f' : '0', STR_PAD_LEFT),
            0 - $length
        ));
    }
}

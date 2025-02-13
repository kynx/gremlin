<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Exception\UnderflowException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\ByteBufferType;
use Kynx\Gremlin\Structure\Type\ByteBufferTypeInterface;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

use function assert;
use function fopen;
use function fwrite;
use function is_resource;
use function min;
use function rewind;
use function strlen;

/**
 * Sequence of unsigned 8-bit integers in format `{length}{value}`
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_bytebuffer_3
 */
final readonly class ByteBufferSerializer implements SerializerInterface
{
    private const int CHUNK_SIZE = 8192;

    /**
     * @param class-string<ByteBufferTypeInterface> $byteBufferClass
     */
    public function __construct(private string $byteBufferClass = ByteBufferType::class)
    {
    }

    public function getBinaryType(): BinaryType
    {
        return BinaryType::ByteBuffer;
    }

    public function getPhpType(): string
    {
        return ByteBufferType::class;
    }

    public function unserialize(StreamInterface $stream, Reader $reader): ByteBufferTypeInterface
    {
        if ($reader->isNull($stream)) {
            return $this->byteBufferClass::ofString('');
        }

        $length = $reader->readUInt($stream);
        if ($length === 0) {
            return $this->byteBufferClass::ofString('');
        }

        $read     = 0;
        $resource = fopen('php://memory', 'w+');
        assert(is_resource($resource));

        while ($read < $length && ! $stream->eof()) {
            $bytes = $reader->readBytes($stream, min(self::CHUNK_SIZE, $length - $read));

            fwrite($resource, $bytes);
            $read += strlen($bytes);
        }
        rewind($resource);

        return $this->byteBufferClass::ofResource($resource);
    }

    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof ByteBufferTypeInterface) {
            throw DomainException::invalidType($this, $type);
        }

        $writer->writeNotNull($stream);
        $length = $type->getLength();
        $writer->writeUInt($stream, $length);
        $written = 0;

        while ($written < $length && ! $type->eof()) {
            $bytes = $type->read(min(self::CHUNK_SIZE, $length - $written));
            if ($bytes === '') {
                break;
            }

            $len = strlen($bytes);
            $writer->writeBytes($stream, $bytes, $len);
            $written += $len;
        }

        if ($written < $length) {
            throw UnderflowException::dataNotWritten($length, $written);
        }
    }
}

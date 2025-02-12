<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\ReaderException;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\AbstractSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\ByteBufferType;
use Kynx\Gremlin\Structure\Type\ByteBufferTypeInterface;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

use function assert;
use function fclose;
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
 *
 * @template-extends AbstractSerializer<ByteBufferTypeInterface>
 */
final readonly class ByteBufferSerializer extends AbstractSerializer
{
    private const int CHUNK_SIZE = 8192;

    /**
     * @param class-string<ByteBufferTypeInterface> $byteBufferClass
     */
    public function __construct(private string $byteBufferClass = ByteBufferType::class)
    {
    }

    public function getGraphType(): GraphType
    {
        return GraphType::ByteBuffer;
    }

    public function getPhpType(): string
    {
        return ByteBufferType::class;
    }

    public function read(StreamInterface $stream, Reader $reader): ByteBufferTypeInterface
    {
        if ($this->isNull($stream)) {
            return $this->byteBufferClass::ofString('');
        }

        $length = IntUtil::unpackUInt($stream->read(4));
        if ($length === 0) {
            return $this->byteBufferClass::ofString('');
        }

        $read     = 0;
        $resource = fopen('php://memory', 'w+');
        assert(is_resource($resource));

        while ($read < $length && ! $stream->eof()) {
            $bytes = $stream->read(min(self::CHUNK_SIZE, $length - $read));
            if ($bytes === '') {
                break;
            }

            fwrite($resource, $bytes);
            $read += strlen($bytes);
        }

        if ($read < $length) {
            @fclose($resource);
            throw ReaderException::allDataNotRead($length, $read);
        }
        rewind($resource);

        return $this->byteBufferClass::ofResource($resource);
    }

    public function write(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof ByteBufferTypeInterface) {
            throw WriterException::invalidType($this, $type);
        }

        $this->writeNotNull($stream);
        $length = $type->getLength();
        $stream->write(IntUtil::packUInt($length));
        $written = 0;

        while ($written < $length && ! $type->eof()) {
            $bytes = $type->read(min(self::CHUNK_SIZE, $length - $written));
            if ($bytes === '') {
                break;
            }

            $stream->write($bytes);
            $written += strlen($bytes);
        }

        if ($written < $length) {
            throw WriterException::allDataNotWritten($length, $written);
        }
    }
}

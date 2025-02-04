<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\ByteType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

/**
 * An unsigned 8-bit integer
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_byte_3
 *
 * @template-extends AbstractSerializer<ByteType>
 */
final readonly class ByteSerializer extends AbstractSerializer
{
    public function getGraphType(): GraphType
    {
        return GraphType::Byte;
    }

    public function getPhpType(): string
    {
        return ByteType::class;
    }

    public function read(StreamInterface $stream, Reader $reader): ByteType
    {
        if ($this->isNull($stream)) {
            return new ByteType(null);
        }

        return new ByteType(IntUtil::unpackUInt($stream->read(ByteType::getSize())));
    }

    public function write(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof ByteType) {
            throw WriterException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $this->writeNull($stream);
            return;
        }

        $this->writeNotNull($stream);
        $stream->write(IntUtil::packUInt($value, 8));
    }
}

<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\ByteType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

/**
 * An unsigned 8-bit integer
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_byte_3
 */
final readonly class ByteSerializer implements SerializerInterface
{
    public function getBinaryType(): BinaryType
    {
        return BinaryType::Byte;
    }

    public function getPhpType(): string
    {
        return ByteType::class;
    }

    public function unserialize(StreamInterface $stream, Reader $reader): ByteType
    {
        if ($reader->isNull($stream)) {
            return new ByteType(null);
        }

        return new ByteType($reader->readByte($stream));
    }

    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof ByteType) {
            throw DomainException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $writer->writeNull($stream);
            return;
        }

        $writer->writeNotNull($stream);
        $writer->writeByte($stream, $value);
    }
}

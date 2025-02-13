<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\TimestampType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

/**
 * An 8-byte two’s complement integer
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_long_3
 */
final readonly class TimestampSerializer implements SerializerInterface
{
    public function getBinaryType(): BinaryType
    {
        return BinaryType::Timestamp;
    }

    public function getPhpType(): string
    {
        return TimestampType::class;
    }

    public function unserialize(StreamInterface $stream, Reader $reader): TimestampType
    {
        if ($reader->isNull($stream)) {
            return new TimestampType(null);
        }

        return new TimestampType($reader->readLong($stream));
    }

    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof TimestampType) {
            throw DomainException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $writer->writeNull($stream);
            return;
        }

        $writer->writeNotNull($stream);
        $writer->writeLong($stream, $value);
    }
}

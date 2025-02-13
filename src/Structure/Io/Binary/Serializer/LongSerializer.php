<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\LongType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

/**
 * An 8-byte two’s complement integer
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_long_3
 */
final readonly class LongSerializer implements SerializerInterface
{
    public function getBinaryType(): BinaryType
    {
        return BinaryType::Long;
    }

    public function getPhpType(): string
    {
        return LongType::class;
    }

    public function unserialize(StreamInterface $stream, Reader $reader): LongType
    {
        if ($reader->isNull($stream)) {
            return new LongType(null);
        }

        return new LongType($reader->readLong($stream));
    }

    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof LongType) {
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

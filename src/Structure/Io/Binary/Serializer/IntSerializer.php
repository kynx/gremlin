<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\IntType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

/**
 * A 4-byte two’s complement integer
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_int
 */
final readonly class IntSerializer implements SerializerInterface
{
    public function getBinaryType(): BinaryType
    {
        return BinaryType::Int;
    }

    public function getPhpType(): string
    {
        return IntType::class;
    }

    public function unserialize(StreamInterface $stream, Reader $reader): IntType
    {
        if ($reader->isNull($stream)) {
            return new IntType(null);
        }

        return new IntType($reader->readInt($stream));
    }

    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof IntType) {
            throw DomainException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $writer->writeNull($stream);
            return;
        }

        $writer->writeNotNull($stream);
        $writer->writeInt($stream, $value);
    }
}

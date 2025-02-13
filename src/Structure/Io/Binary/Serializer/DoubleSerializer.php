<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\DoubleType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

/**
 * 8 bytes representing IEEE 754 double-precision binary floating-point format
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_double_3
 */
final readonly class DoubleSerializer implements SerializerInterface
{
    public function getBinaryType(): BinaryType
    {
        return BinaryType::Double;
    }

    public function getPhpType(): string
    {
        return DoubleType::class;
    }

    public function unserialize(StreamInterface $stream, Reader $reader): DoubleType
    {
        if ($reader->isNull($stream)) {
            return new DoubleType(null);
        }
        return new DoubleType($reader->readDouble($stream));
    }

    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof DoubleType) {
            throw DomainException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $writer->writeNull($stream);
            return;
        }

        $writer->writeNotNull($stream);
        $writer->writeDouble($stream, $value);
    }
}

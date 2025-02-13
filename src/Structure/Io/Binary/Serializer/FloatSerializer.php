<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\FloatType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Kynx\Gremlin\Structure\Type\TypeInterface as T;
use Psr\Http\Message\StreamInterface;

/**
 * 4 bytes representing IEEE 754 single-precision binary floating-point format
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_float_3
 */
final readonly class FloatSerializer implements SerializerInterface
{
    public function getBinaryType(): BinaryType
    {
        return BinaryType::Float;
    }

    public function getPhpType(): string
    {
        return FloatType::class;
    }

    public function unserialize(StreamInterface $stream, Reader $reader): T
    {
        if ($reader->isNull($stream)) {
            return new FloatType(null);
        }

        return new FloatType($reader->readFloat($stream));
    }

    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof FloatType) {
            throw DomainException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $writer->writeNull($stream);
            return;
        }

        $writer->writeNotNull($stream);
        $writer->writeFloat($stream, $value);
    }
}

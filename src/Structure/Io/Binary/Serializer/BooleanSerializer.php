<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\BooleanType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

/**
 * A single byte containing the value 0x01 when it’s true and 0 otherwise.
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_boolean
 */
final readonly class BooleanSerializer implements SerializerInterface
{
    public function getBinaryType(): BinaryType
    {
        return BinaryType::Boolean;
    }

    public function getPhpType(): string
    {
        return BooleanType::class;
    }

    public function unserialize(StreamInterface $stream, Reader $reader): BooleanType
    {
        if ($reader->isNull($stream)) {
            return new BooleanType(null);
        }

        return new BooleanType($reader->readBytes($stream, 1) === "\x01");
    }

    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof BooleanType) {
            throw DomainException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $writer->writeNull($stream);
            return;
        }

        $writer->writeNotNull($stream);
        $writer->writeBytes($stream, $value ? "\x01" : "\x00", 1);
    }
}

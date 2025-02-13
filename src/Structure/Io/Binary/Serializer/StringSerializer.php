<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\StringType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

use function strlen;

/**
 * A UTF-8 encoded string in format `{length}{text_value}`
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_string
 */
final readonly class StringSerializer implements SerializerInterface
{
    public function getBinaryType(): BinaryType
    {
        return BinaryType::String;
    }

    public function getPhpType(): string
    {
        return StringType::class;
    }

    public function unserialize(StreamInterface $stream, Reader $reader): StringType
    {
        if ($reader->isNull($stream)) {
            return new StringType(null);
        }

        return new StringType($reader->readBytes($stream, $reader->readUInt($stream)));
    }

    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof StringType) {
            throw DomainException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $writer->writeNull($stream);
            return;
        }

        $length = strlen($value);
        $writer->writeNotNull($stream);
        $writer->writeUInt($stream, $length);
        $writer->writeBytes($stream, $value, $length);
    }
}

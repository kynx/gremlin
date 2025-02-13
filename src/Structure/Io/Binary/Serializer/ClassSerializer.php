<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\ClassType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

use function strlen;

/**
 * A `String` containing the fqcn
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_class_3
 */
final readonly class ClassSerializer implements SerializerInterface
{
    public function getBinaryType(): BinaryType
    {
        return BinaryType::ClassName;
    }

    public function getPhpType(): string
    {
        return ClassType::class;
    }

    public function unserialize(StreamInterface $stream, Reader $reader): ClassType
    {
        if ($reader->isNull($stream)) {
            return new ClassType(null);
        }

        return new ClassType($reader->readBytes($stream, $reader->readUInt($stream)));
    }

    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof ClassType) {
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

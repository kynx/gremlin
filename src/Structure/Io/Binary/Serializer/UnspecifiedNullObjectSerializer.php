<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Kynx\Gremlin\Structure\Type\UnspecifiedNullObject;
use Psr\Http\Message\StreamInterface;

/**
 * Represented using the null {value_flag} set and no sequence of bytes
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_unspecified_null_object
 */
final readonly class UnspecifiedNullObjectSerializer implements SerializerInterface
{
    public function getBinaryType(): BinaryType
    {
        return BinaryType::UnspecifiedNullObject;
    }

    public function getPhpType(): string
    {
        return UnspecifiedNullObject::class;
    }

    public function unserialize(StreamInterface $stream, Reader $reader): UnspecifiedNullObject
    {
        return new UnspecifiedNullObject();
    }

    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof UnspecifiedNullObject) {
            throw DomainException::invalidType($this, $type);
        }
    }
}

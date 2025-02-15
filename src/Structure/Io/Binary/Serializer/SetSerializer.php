<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\SetType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

/**
 * A set that contains no duplicate elements in format `{length}{item_0}…{item_n}`
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_set_2
 */
final readonly class SetSerializer implements SerializerInterface
{
    public function getBinaryType(): BinaryType
    {
        return BinaryType::Set;
    }

    public function getPhpType(): string
    {
        return SetType::class;
    }

    public function unserialize(StreamInterface $stream, Reader $reader): SetType
    {
        if ($reader->isNull($stream)) {
            return new SetType(null);
        }

        $length = $reader->readUInt($stream);
        $items  = [];
        for ($i = 0; $i < $length; $i++) {
            $items[] = $reader->read($stream);
        }

        return new SetType($items);
    }

    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof SetType) {
            throw DomainException::invalidType($this, $type);
        }

        $values = $type->getValue();
        if ($values === null) {
            $writer->writeNull($stream);
            return;
        }

        $writer->writeNotNull($stream);
        $writer->writeUInt($stream, $type->getLength());
        foreach ($values as $item) {
            $writer->write($stream, $item);
        }
    }
}

<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\ListType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

/**
 * An ordered collection of items in format `{length}{item_0}…{item_n}`
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_list_2
 */
final readonly class ListSerializer implements SerializerInterface
{
    public function getBinaryType(): BinaryType
    {
        return BinaryType::List;
    }

    public function getPhpType(): string
    {
        return ListType::class;
    }

    public function unserialize(StreamInterface $stream, Reader $reader): ListType
    {
        if ($reader->isNull($stream)) {
            return new ListType(null);
        }

        $length = $reader->readUInt($stream);
        $items  = [];
        for ($i = 0; $i < $length; $i++) {
            $items[] = $reader->read($stream);
        }

        return new ListType($items);
    }

    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof ListType) {
            throw DomainException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $writer->writeNull($stream);
            return;
        }

        $writer->writeNotNull($stream);
        $writer->writeUInt($stream, $type->getLength());
        foreach ($value as $item) {
            $writer->write($stream, $item);
        }
    }
}

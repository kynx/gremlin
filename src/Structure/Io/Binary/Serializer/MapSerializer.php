<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\MapItem;
use Kynx\Gremlin\Structure\Type\MapType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

/**
 * A dictionary of keys to values in format `{length}{item_0}…{item_n}`
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_map_2
 */
final readonly class MapSerializer implements SerializerInterface
{
    public function getBinaryType(): BinaryType
    {
        return BinaryType::Map;
    }

    public function getPhpType(): string
    {
        return MapType::class;
    }

    public function unserialize(StreamInterface $stream, Reader $reader): MapType
    {
        if ($reader->isNull($stream)) {
            return new MapType(null);
        }

        $length = $reader->readUInt($stream);
        $items  = [];
        for ($i = 0; $i < $length; $i++) {
            $items[] = new MapItem($reader->read($stream), $reader->read($stream));
        }

        return new MapType($items);
    }

    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof MapType) {
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
            $writer->write($stream, $item->key);
            $writer->write($stream, $item->value);
        }
    }
}

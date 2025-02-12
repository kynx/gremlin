<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\TimestampType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

/**
 * An 8-byte two’s complement integer
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_long_3
 *
 * @template-extends AbstractSerializer<TimestampType>
 */
final readonly class TimestampSerializer extends AbstractSerializer
{
    public function getGraphType(): GraphType
    {
        return GraphType::Timestamp;
    }

    public function getPhpType(): string
    {
        return TimestampType::class;
    }

    public function read(StreamInterface $stream, Reader $reader): TimestampType
    {
        if ($this->isNull($stream)) {
            return new TimestampType(null);
        }

        return new TimestampType(IntUtil::unpackInt($stream->read(TimestampType::getSize())));
    }

    public function write(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof TimestampType) {
            throw WriterException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $this->writeNull($stream);
            return;
        }

        $this->writeNotNull($stream);
        $stream->write(IntUtil::packInt($value, TimestampType::getSize() * 8));
    }
}

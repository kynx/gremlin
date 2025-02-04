<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\AbstractSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\LongType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

/**
 * An 8-byte two’s complement integer
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_long_3
 *
 * @template-extends AbstractSerializer<LongType>
 */
final readonly class LongSerializer extends AbstractSerializer
{
    public function getGraphType(): GraphType
    {
        return GraphType::Long;
    }

    public function getPhpType(): string
    {
        return LongType::class;
    }

    public function read(StreamInterface $stream, Reader $reader): LongType
    {
        if ($this->isNull($stream)) {
            return new LongType(null);
        }

        return new LongType(IntUtil::unpackInt($stream->read(LongType::getSize() * 8)));
    }

    public function write(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof LongType) {
            throw WriterException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $this->writeNull($stream);
            return;
        }

        $this->writeNotNull($stream);
        $stream->write(IntUtil::packInt($value, 64));
    }
}

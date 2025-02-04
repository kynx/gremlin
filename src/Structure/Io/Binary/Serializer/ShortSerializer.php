<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\AbstractSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\ShortType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

/**
 * A 2-byte two’s complement integer
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_short_3
 *
 * @template-extends AbstractSerializer<ShortType>
 */
final readonly class ShortSerializer extends AbstractSerializer
{
    public function getGraphType(): GraphType
    {
        return GraphType::Short;
    }

    public function getPhpType(): string
    {
        return ShortType::class;
    }

    public function read(StreamInterface $stream, Reader $reader): ShortType
    {
        if ($this->isNull($stream)) {
            return new ShortType(null);
        }

        return new ShortType(IntUtil::unpackInt($stream->read(2)));
    }

    public function write(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof ShortType) {
            throw WriterException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $this->writeNull($stream);
            return;
        }

        $this->writeNotNull($stream);
        $stream->write(IntUtil::packInt($value, 16));
    }
}

<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\IntType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

/**
 * A 4-byte two’s complement integer
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_int
 *
 * @template-extends AbstractSerializer<IntType>
 */
final readonly class IntSerializer extends AbstractSerializer
{
    public function getGraphType(): GraphType
    {
        return GraphType::Int;
    }

    public function getPhpType(): string
    {
        return IntType::class;
    }

    public function read(StreamInterface $stream, Reader $reader): IntType
    {
        if ($this->isNull($stream)) {
            return new IntType(null);
        }

        return new IntType(IntUtil::unpackInt($stream->read(IntType::getSize())));
    }

    public function write(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof IntType) {
            throw WriterException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $this->writeNull($stream);
            return;
        }

        $this->writeNotNull($stream);
        $stream->write(IntUtil::packInt($value));
    }
}

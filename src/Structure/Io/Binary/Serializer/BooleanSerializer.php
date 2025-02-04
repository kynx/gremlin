<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\BooleanType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

/**
 * A single byte containing the value 0x01 when it’s true and 0 otherwise.
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_boolean
 *
 * @template-extends AbstractSerializer<BooleanType>
 */
final readonly class BooleanSerializer extends AbstractSerializer
{
    public function getGraphType(): GraphType
    {
        return GraphType::Boolean;
    }

    public function getPhpType(): string
    {
        return BooleanType::class;
    }

    public function read(StreamInterface $stream, Reader $reader): BooleanType
    {
        if ($this->isNull($stream)) {
            return new BooleanType(null);
        }

        return new BooleanType($stream->read(1) === "\x01");
    }

    public function write(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof BooleanType) {
            throw WriterException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $this->writeNull($stream);
            return;
        }

        $this->writeNotNull($stream);
        $stream->write($value ? "\x01" : "\x00");
    }
}

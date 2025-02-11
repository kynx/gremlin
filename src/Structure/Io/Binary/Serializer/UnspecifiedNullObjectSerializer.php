<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Kynx\Gremlin\Structure\Type\UnspecifiedNullObject;
use Psr\Http\Message\StreamInterface;

/**
 * Represented using the null {value_flag} set and no sequence of bytes
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_unspecified_null_object
 *
 * @template-extends AbstractSerializer<UnspecifiedNullObject>
 */
final readonly class UnspecifiedNullObjectSerializer extends AbstractSerializer
{
    public function getGraphType(): GraphType
    {
        return GraphType::UnspecifiedNullObject;
    }

    public function getPhpType(): string
    {
        return UnspecifiedNullObject::class;
    }

    public function read(StreamInterface $stream, Reader $reader): UnspecifiedNullObject
    {
        return new UnspecifiedNullObject();
    }

    public function write(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof UnspecifiedNullObject) {
            throw WriterException::invalidType($this, $type);
        }
    }
}

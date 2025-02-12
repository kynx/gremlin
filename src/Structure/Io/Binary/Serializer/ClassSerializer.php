<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\ClassType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

use function strlen;

/**
 * A `String` containing the fqcn
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_class_3
 *
 * @template-extends AbstractSerializer<ClassType>
 */
final readonly class ClassSerializer extends AbstractSerializer
{
    public function getGraphType(): GraphType
    {
        return GraphType::ClassName;
    }

    public function getPhpType(): string
    {
        return ClassType::class;
    }

    public function read(StreamInterface $stream, Reader $reader): ClassType
    {
        if ($this->isNull($stream)) {
            return new ClassType(null);
        }

        $length = IntUtil::unpackUInt($stream->read(4));
        return new ClassType($stream->read($length));
    }

    public function write(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof ClassType) {
            throw WriterException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $this->writeNull($stream);
            return;
        }

        $this->writeNotNull($stream);
        $stream->write(IntUtil::packUInt(strlen($value)) . $value);
    }
}

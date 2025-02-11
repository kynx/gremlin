<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\StringType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

use function strlen;

/**
 * A UTF-8 encoded string in format `{length}{text_value}`
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_string
 *
 * @template-extends AbstractSerializer<StringType>
 */
final readonly class StringSerializer extends AbstractSerializer
{
    public function getGraphType(): GraphType
    {
        return GraphType::String;
    }

    public function getPhpType(): string
    {
        return StringType::class;
    }

    public function read(StreamInterface $stream, Reader $reader): StringType
    {
        if ($this->isNull($stream)) {
            return new StringType(null);
        }

        $length = IntUtil::unpackUInt($stream->read(4));
        return new StringType($stream->read($length));
    }

    public function write(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof StringType) {
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

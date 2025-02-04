<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\FloatType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Kynx\Gremlin\Structure\Type\TypeInterface as T;
use Psr\Http\Message\StreamInterface;

use function assert;
use function is_array;
use function pack;
use function unpack;

/**
 * 4 bytes representing IEEE 754 single-precision binary floating-point format
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_float_3
 *
 * @template-extends AbstractSerializer<FloatType>
 */
final readonly class FloatSerializer extends AbstractSerializer
{
    public function getGraphType(): GraphType
    {
        return GraphType::Float;
    }

    public function getPhpType(): string
    {
        return FloatType::class;
    }

    public function read(StreamInterface $stream, Reader $reader): T
    {
        if ($this->isNull($stream)) {
            return new FloatType(null);
        }

        $unpacked = unpack('G', $stream->read(4));
        assert(is_array($unpacked) && isset($unpacked[1]));
        return new FloatType((float) $unpacked[1]);
    }

    public function write(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof FloatType) {
            throw WriterException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $this->writeNull($stream);
            return;
        }

        $this->writeNotNull($stream);
        $stream->write(pack('G', $value));
    }
}

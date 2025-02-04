<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\DoubleType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

use function assert;
use function is_array;
use function pack;
use function unpack;

/**
 * 8 bytes representing IEEE 754 double-precision binary floating-point format
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_double_3
 *
 * @template-extends AbstractSerializer<DoubleType>
 */
final readonly class DoubleSerializer extends AbstractSerializer
{
    public function getGraphType(): GraphType
    {
        return GraphType::Double;
    }

    public function getPhpType(): string
    {
        return DoubleType::class;
    }

    public function read(StreamInterface $stream, Reader $reader): DoubleType
    {
        if ($this->isNull($stream)) {
            return new DoubleType(null);
        }

        $unpacked = unpack('E', $stream->read(8));
        assert(is_array($unpacked) && isset($unpacked[1]));
        return new DoubleType((float) $unpacked[1]);
    }

    public function write(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof DoubleType) {
            throw WriterException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $this->writeNull($stream);
            return;
        }

        $this->writeNotNull($stream);
        $stream->write(pack('E', $value));
    }
}

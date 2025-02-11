<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\AbstractSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\GraphType;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\CharType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

use function ord;

/**
 * One to four bytes representing a single UTF8 char, according to the Unicode standard
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_char_3
 *
 * @template-extends AbstractSerializer<CharType>
 */
final readonly class CharSerializer extends AbstractSerializer
{
    public function getGraphType(): GraphType
    {
        return GraphType::Char;
    }

    public function getPhpType(): string
    {
        return CharType::class;
    }

    public function read(StreamInterface $stream, Reader $reader): CharType
    {
        if ($this->isNull($stream)) {
            return new CharType(null);
        }

        $byte = $stream->read(1);
        $ord  = ord($byte);

        return new CharType(match (true) {
            $ord >= 0xf0 => $byte . $stream->read(3),
            $ord >= 0xe0 => $byte . $stream->read(2),
            $ord >= 0xc0 => $byte . $stream->read(1),
            default      => $byte,
        });
    }

    public function write(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof CharType) {
            throw WriterException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $this->writeNull($stream);
            return;
        }

        $this->writeNotNull($stream);
        $stream->write($value);
    }
}

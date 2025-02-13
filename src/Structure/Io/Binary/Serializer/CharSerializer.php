<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\CharType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

use function ord;
use function strlen;

/**
 * One to four bytes representing a single UTF8 char, according to the Unicode standard
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_char_3
 */
final readonly class CharSerializer implements SerializerInterface
{
    public function getBinaryType(): BinaryType
    {
        return BinaryType::Char;
    }

    public function getPhpType(): string
    {
        return CharType::class;
    }

    public function unserialize(StreamInterface $stream, Reader $reader): CharType
    {
        if ($reader->isNull($stream)) {
            return new CharType(null);
        }

        $byte = $reader->readBytes($stream, 1);
        $ord  = ord($byte);

        return new CharType(match (true) {
            $ord >= 0xf0 => $byte . $reader->readBytes($stream, 3),
            $ord >= 0xe0 => $byte . $reader->readBytes($stream, 2),
            $ord >= 0xc0 => $byte . $reader->readBytes($stream, 1),
            default      => $byte,
        });
    }

    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof CharType) {
            throw DomainException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $writer->writeNull($stream);
            return;
        }

        $length = strlen($value);
        $writer->writeNotNull($stream);
        $writer->writeBytes($stream, $value, $length);
    }
}

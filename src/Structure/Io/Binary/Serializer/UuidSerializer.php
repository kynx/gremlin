<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Kynx\Gremlin\Structure\Type\UuidType;
use Psr\Http\Message\StreamInterface;

use function bin2hex;
use function hex2bin;
use function sprintf;
use function str_replace;
use function substr;

/**
 * A 128-bit universally unique identifier encoded as 16 bytes
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_uuid_3
 */
final readonly class UuidSerializer implements SerializerInterface
{
    public function getBinaryType(): BinaryType
    {
        return BinaryType::UUID;
    }

    /**
     * @inheritDoc
     */
    public function getPhpType(): string
    {
        return UuidType::class;
    }

    /**
     * @inheritDoc
     */
    public function unserialize(StreamInterface $stream, Reader $reader): TypeInterface
    {
        if ($reader->isNull($stream)) {
            return new UuidType(null);
        }

        $hex = bin2hex($reader->readBytes($stream, 16));

        return new UuidType(sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        ));
    }

    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof UuidType) {
            throw DomainException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $writer->writeNull($stream);
            return;
        }

        $writer->writeNotNull($stream);
        $writer->writeBytes(
            $stream,
            (string) hex2bin(str_replace('-', '', $value)),
            16
        );
    }
}

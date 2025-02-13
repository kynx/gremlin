<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use DateTimeImmutable;
use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\DateType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

use function abs;
use function floor;
use function sprintf;

/**
 * An 8-byte two’s complement signed integer representing a millisecond-precision offset from the unix epoch
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_date_3
 */
final readonly class DateSerializer implements SerializerInterface
{
    public function getBinaryType(): BinaryType
    {
        return BinaryType::Date;
    }

    public function getPhpType(): string
    {
        return DateType::class;
    }

    public function unserialize(StreamInterface $stream, Reader $reader): DateType
    {
        if ($reader->isNull($stream)) {
            return new DateType(null);
        }

        $microseconds = $reader->readLong($stream);
        $abs          = abs($microseconds);
        $timestamp    = (int) floor($abs / 1000);

        return new DateType(new DateTimeImmutable(sprintf(
            '@%s%d.%03d',
            $microseconds < 0 ? '-' : '',
            $timestamp,
            $abs - ($timestamp * 1000)
        )));
    }

    /**
     * @inheritDoc
     */
    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof DateType) {
            throw DomainException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $writer->writeNull($stream);
            return;
        }

        $timestamp = $value->getTimestamp() * 1000 + (int) $value->format('v');
        $writer->writeNotNull($stream);
        $writer->writeLong($stream, $timestamp);
    }
}

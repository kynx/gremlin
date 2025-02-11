<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use DateTimeImmutable;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
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
 *
 * @template-extends AbstractSerializer<DateType>
 */
final readonly class DateSerializer extends AbstractSerializer
{
    public function getGraphType(): GraphType
    {
        return GraphType::Date;
    }

    public function getPhpType(): string
    {
        return DateType::class;
    }

    public function read(StreamInterface $stream, Reader $reader): DateType
    {
        if ($this->isNull($stream)) {
            return new DateType(null);
        }

        $microseconds = IntUtil::unpackInt($stream->read(8));
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
    public function write(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof DateType) {
            throw WriterException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $this->writeNull($stream);
            return;
        }

        $timestamp = $value->getTimestamp() * 1000 + (int) $value->format('v');
        $this->writeNotNull($stream);
        $stream->write(IntUtil::packInt($timestamp, 64));
    }
}

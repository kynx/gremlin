<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Brick\Math\BigDecimal;
use Brick\Math\BigInteger;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NegativeNumberException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\ReaderException;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\BigDecimalType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

use function pow;
use function strlen;

/**
 * An arbitrary-precision signed decimal number, consisting of an arbitrary precision integer unscaled value and a
 * 32-bit integer scale
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_bigdecimal_3
 *
 * @template-extends AbstractSerializer<BigDecimalType>
 */
final readonly class BigDecimalSerializer extends AbstractSerializer
{
    public function getGraphType(): GraphType
    {
        return GraphType::BigDecimal;
    }

    public function getPhpType(): string
    {
        return BigDecimalType::class;
    }

    public function read(StreamInterface $stream, Reader $reader): BigDecimalType
    {
        if ($this->isNull($stream)) {
            return new BigDecimalType(null);
        }

        $scale  = IntUtil::unpackInt($stream->read(4));
        $length = IntUtil::unpackUInt($stream->read(4));

        try {
            $bigInt = BigInteger::fromBytes($stream->read($length));

            // @see https://github.com/brick/math/issues/86
            if ($scale < 0) {
                $bigInt = $bigInt->multipliedBy(pow(10, 0 - $scale));
                $scale  = 0;
            }
        } catch (MathException $exception) {
            throw ReaderException::fromThrowable($exception);
        }

        return new BigDecimalType(BigDecimal::ofUnscaledValue($bigInt, $scale));
    }

    public function write(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof BigDecimalType) {
            throw WriterException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $this->writeNull($stream);
            return;
        }

        $this->writeNotNull($stream);
        $stream->write(IntUtil::packInt($value->getScale()));

        try {
            $bytes = $value->getUnscaledValue()->toBytes();
        } catch (NegativeNumberException $exception) {
            throw WriterException::fromThrowable($exception);
        }

        $stream->write(IntUtil::packUInt(strlen($bytes)));
        $stream->write($bytes);
    }
}

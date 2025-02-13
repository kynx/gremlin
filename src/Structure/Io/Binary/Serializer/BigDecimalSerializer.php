<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Brick\Math\BigDecimal;
use Brick\Math\BigInteger;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NegativeNumberException;
use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
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
 */
final readonly class BigDecimalSerializer implements SerializerInterface
{
    public function getBinaryType(): BinaryType
    {
        return BinaryType::BigDecimal;
    }

    public function getPhpType(): string
    {
        return BigDecimalType::class;
    }

    public function unserialize(StreamInterface $stream, Reader $reader): BigDecimalType
    {
        if ($reader->isNull($stream)) {
            return new BigDecimalType(null);
        }

        $scale  = $reader->readInt($stream);
        $length = $reader->readUInt($stream);

        try {
            $bigInt = BigInteger::fromBytes($reader->readBytes($stream, $length));

            // @see https://github.com/brick/math/issues/86
            if ($scale < 0) {
                $bigInt = $bigInt->multipliedBy(pow(10, 0 - $scale));
                $scale  = 0;
            }
        } catch (MathException $exception) {
            throw DomainException::ofThrowable($exception);
        }

        return new BigDecimalType(BigDecimal::ofUnscaledValue($bigInt, $scale));
    }

    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof BigDecimalType) {
            throw DomainException::invalidType($this, $type);
        }

        $value = $type->getValue();
        if ($value === null) {
            $writer->writeNull($stream);
            return;
        }

        $writer->writeNotNull($stream);
        $writer->writeInt($stream, $value->getScale());

        try {
            $bytes = $value->getUnscaledValue()->toBytes();
        } catch (NegativeNumberException $exception) {
            throw DomainException::ofThrowable($exception);
        }

        $length = strlen($bytes);
        $writer->writeUInt($stream, $length);
        $writer->writeBytes($stream, $bytes, $length);
    }
}

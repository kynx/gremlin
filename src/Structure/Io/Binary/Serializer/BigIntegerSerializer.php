<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Brick\Math\BigInteger;
use Brick\Math\Exception\NegativeNumberException;
use Brick\Math\Exception\NumberFormatException;
use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\BigIntegerType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

use function strlen;

/**
 * A variable-length two’s complement encoding of a signed integer
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_biginteger_3
 */
final readonly class BigIntegerSerializer implements SerializerInterface
{
    public function getBinaryType(): BinaryType
    {
        return BinaryType::BigInteger;
    }

    public function getPhpType(): string
    {
        return BigIntegerType::class;
    }

    public function unserialize(StreamInterface $stream, Reader $reader): BigIntegerType
    {
        if ($reader->isNull($stream)) {
            return new BigIntegerType(null);
        }

        $length = $reader->readUInt($stream);
        try {
            return new BigIntegerType(BigInteger::fromBytes($reader->readBytes($stream, $length)));
        } catch (NumberFormatException $exception) {
            throw DomainException::ofThrowable($exception);
        }
    }

    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof BigIntegerType) {
            throw DomainException::invalidType($this, $stream);
        }

        $value = $type->getValue();
        if ($value === null) {
            $writer->writeNull($stream);
            return;
        }

        try {
            $bytes = $value->toBytes();
        } catch (NegativeNumberException $exception) {
            throw DomainException::ofThrowable($exception);
        }

        $length = strlen($bytes);
        $writer->writeNotNull($stream);
        $writer->writeUInt($stream, $length);
        $writer->writeBytes($stream, $bytes, $length);
    }
}

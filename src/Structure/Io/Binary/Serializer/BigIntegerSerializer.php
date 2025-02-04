<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Brick\Math\BigInteger;
use Brick\Math\Exception\NegativeNumberException;
use Brick\Math\Exception\NumberFormatException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\ReaderException;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\BigIntegerType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

use function strlen;

/**
 * A variable-length two’s complement encoding of a signed integer
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_biginteger_3
 *
 * @template-extends AbstractSerializer<BigIntegerType>
 */
final readonly class BigIntegerSerializer extends AbstractSerializer
{
    public function getGraphType(): GraphType
    {
        return GraphType::BigInteger;
    }

    public function getPhpType(): string
    {
        return BigIntegerType::class;
    }

    public function read(StreamInterface $stream, Reader $reader): BigIntegerType
    {
        if ($this->isNull($stream)) {
            return new BigIntegerType(null);
        }

        $length = IntUtil::unpackUInt($stream->read(4));
        try {
            return new BigIntegerType(BigInteger::fromBytes($stream->read($length)));
        } catch (NumberFormatException $exception) {
            throw ReaderException::fromThrowable($exception);
        }
    }

    public function write(StreamInterface $stream, TypeInterface $type, Writer $writer): void
    {
        if (! $type instanceof BigIntegerType) {
            throw WriterException::invalidType($this, $stream);
        }

        $value = $type->getValue();
        if ($value === null) {
            $this->writeNull($stream);
            return;
        }

        try {
            $bytes = $value->toBytes();
        } catch (NegativeNumberException $exception) {
            throw WriterException::fromThrowable($exception);
        }

        $this->writeNotNull($stream);
        $stream->write(IntUtil::packUInt(strlen($bytes)));
        $stream->write($bytes);
    }
}

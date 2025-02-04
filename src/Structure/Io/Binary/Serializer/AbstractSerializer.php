<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @template T of TypeInterface
 * @template-implements SerializerInterface<T>
 */
abstract readonly class AbstractSerializer implements SerializerInterface
{
    protected const string FLAG_NONE = "\x00";
    protected const string FLAG_NULL = "\x01";

    /**
     * Returns true if stream contains null `{value_flag}`
     */
    protected function isNull(StreamInterface $stream): bool
    {
        return $stream->read(1) === self::FLAG_NULL;
    }

    /**
     * Writes null `{value_flag}` to stream}
     */
    protected function writeNull(StreamInterface $stream): void
    {
        $stream->write(self::FLAG_NULL);
    }

    /**
     * Writes empty `{value_flag}` to stream
     */
    protected function writeNotNull(StreamInterface $stream): void
    {
        $stream->write(self::FLAG_NONE);
    }
}

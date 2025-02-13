<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Serialize and unserialize graph structures
 *
 * Implementations MUST NOT mutate the stream they receive: instead they must use the `$reader` and `$writer` to safely
 * perform operations on it.
 *
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_data_type_formats
 */
interface SerializerInterface
{
    /**
     * Returns binary type that the serializer handles
     */
    public function getBinaryType(): BinaryType;

    /**
     * Returns FQCN of the type the serializer handles
     *
     * @return class-string<TypeInterface>
     */
    public function getPhpType(): string;

    /**
     * Returns type from stream's `{type_info}{value_flag}{value}`
     *
     * @throws IOException
     */
    public function unserialize(StreamInterface $stream, Reader $reader): TypeInterface;

    /**
     * Writes binary representation of the type in form `{type_info}{value_flag}{value}`
     *
     * @throws IOException
     */
    public function serialize(StreamInterface $stream, TypeInterface $type, Writer $writer): void;
}

<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\ReaderException;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_data_type_formats
 *
 * @template T of TypeInterface
 */
interface SerializerInterface
{
    /**
     * Returns binary type that the serializer reads
     */
    public function getGraphType(): GraphType;

    /**
     * Returns FQCN of type the serializer writes
     *
     * @return class-string<T>
     */
    public function getPhpType(): string;

    /**
     * Returns PHP type from stream's `{type_info}{value_flag}{value}`
     *
     * @return T
     * @throws ReaderException
     */
    public function read(StreamInterface $stream, Reader $reader): TypeInterface;

    /**
     * Writes binary string representation of the `{type_info}{value_flag}{value}` to stream
     *
     * @param T $type
     * @throws WriterException
     */
    public function write(StreamInterface $stream, TypeInterface $type, Writer $writer): void;
}

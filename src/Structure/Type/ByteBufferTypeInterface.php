<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Type;

interface ByteBufferTypeInterface extends TypeInterface
{
    /**
     * @param resource $resource
     * @throws TypeException If resource invalid or length cannot be calculated.
     */
    public static function ofResource($resource, ?int $length = null): ByteBufferTypeInterface;

    /**
     * Returns instance created from string of bytes
     */
    public static function ofString(string $string): ByteBufferTypeInterface;

    /**
     * Returns instance created from array of integers
     *
     * @param array<int<0, 255>> $bytes
     * @throws TypeException If ints out of range.
     */
    public static function ofByteArray(array $bytes): ByteBufferTypeInterface;

    /**
     * Returns length of buffer
     */
    public function getLength(): int;

    /**
     * Returns bytes from buffer, or empty string if all bytes read
     */
    public function read(int $length = 1): string;

    /**
     * Returns true if pointer is beyond end of buffer
     */
    public function eof(): bool;

    /**
     * Returns iterator for traversing contents of buffer
     *
     * @return iterable<int<0, 255>>
     */
    public function getIterator(): iterable;

    /**
     * Returns array if integers containing contents of buffer
     *
     * @return list<int<0, 255>>
     */
    public function getByteArray(): array;
}

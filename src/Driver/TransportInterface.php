<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Driver;

use Kynx\Gremlin\Driver\Exception\TransportExceptionInterface;
use Psr\Http\Message\StreamInterface;

/**
 * A transport is responsible for sending a POST request to the remote server and returning a stream of bytes
 *
 * It should do so asynchronously, filling the stream as the bytes are received: `guzzlehttp/psr7`'s `PumpStream` is a
 * good fit for this. It should also handle consumers closing or discarding the stream - for instance, if
 * deserialization fails - and abandon the request as early as possible. We provide a delegating `TransportStream` that
 * accepts an `onClose` callback to help with that.
 *
 * @psalm-type HeaderParamValueType = string|array<string>
 * @psalm-type HeaderParamArrayType = array<non-empty-string, HeaderParamValueType>
 */
interface TransportInterface
{
    /**
     * @param HeaderParamArrayType $headers
     * @throws TransportExceptionInterface
     */
    public function submit(string $url, array $headers, string $payload): StreamInterface;
}

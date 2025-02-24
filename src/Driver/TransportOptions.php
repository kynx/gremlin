<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Driver;

final readonly class TransportOptions
{
    public const DEFAULT_HEADER_SIZE_LIMIT = 2 * 8192;
    public const DEFAULT_BODY_SIZE_LIMIT   = 10485760;

    /**
     * @param float $connectTimeout      Connection timeout (seconds)
     * @param float $handshakeTimeout    TLS handshake timeout (seconds)
     * @param float $transferTimeout     Transfer timeout (seconds)
     * @param float $inactivityTimeout   Inactivity timeout (seconds)
     * @param int $headerSizeLimit       Maximum size of headers (bytes)
     * @param int $bodySizeLimit         Maximum size of body (bytes)
     */
    public function __construct(
        public float $connectTimeout = 10,
        public float $handshakeTimeout = 10,
        public float $transferTimeout = 10,
        public float $inactivityTimeout = 10,
        public int $headerSizeLimit = self::DEFAULT_HEADER_SIZE_LIMIT,
        public int $bodySizeLimit = self::DEFAULT_BODY_SIZE_LIMIT,
    ) {
    }
}

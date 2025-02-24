<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Driver\Amphp;

use Kynx\Gremlin\Driver\TransportInterface;

/**
 * @psalm-import-type HeaderParamArrayType from TransportInterface
 */
final readonly class MockResponse
{
    /**
     * @param HeaderParamArrayType $headers
     */
    public function __construct(
        public int $status = 200,
        public array $headers = [],
        public string $body = '',
        public string $reason = ''
    ) {
    }
}

<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Driver\Exception;

use Kynx\Gremlin\Driver\DriverExceptionInterface;

/**
 * Marker for exceptions thrown by transports
 */
interface TransportExceptionInterface extends DriverExceptionInterface
{
}

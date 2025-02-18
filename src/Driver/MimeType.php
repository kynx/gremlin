<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Driver;

enum MimeType: string
{
    public const MimeType DEFAULT = self::GraphBinary40;

    case GraphSON2     = 'application/vnd.gremlin-v2.0+json';
    case GraphSON3     = 'application/vnd.gremlin-v3.0+json';
    case GraphBinary10 = 'application/vnd.graphbinary-v1.0';
    case GraphBinary40 = 'application/vnd.graphbinary-v4.0';
}

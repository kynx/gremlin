<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Driver;

enum MimeType: string
{
    public const MimeType DEFAULT = self::GraphBinary;

    case GraphSON2   = 'application/vnd.gremlin-v2.0+json';
    case GraphSON3   = 'application/vnd.gremlin-v3.0+json';
    case GraphBinary = 'application/vnd.graphbinary-v1.0';
}

<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure;

use Kynx\Gremlin\Structure\Edge;
use Kynx\Gremlin\Structure\Path;
use Kynx\Gremlin\Structure\Vertex;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Path::class)]
final class PathTest extends TestCase
{
    public function testToStringReturnsSummary(): void
    {
        $expected = 'path[e[2][v[1]-edge-`null`], v[1], e[3][`null`-edge-v[1]]]';
        $vertex   = new Vertex(1, []);
        $first    = new Edge(2, $vertex, null);
        $second   = new Edge(3, null, $vertex);
        $path     = new Path(['a', 'b', 'c'], [$first, $vertex, $second]);

        $actual = (string) $path;
        self::assertSame($expected, $actual);
    }
}

<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure;

use Kynx\Gremlin\Structure\Edge;
use Kynx\Gremlin\Structure\Property;
use Kynx\Gremlin\Structure\Vertex;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Edge::class)]
final class EdgeTest extends TestCase
{
    public function testConstructorKeysProperties(): void
    {
        $property = new Property('foo', 'bar');
        $expected = ['foo' => $property];
        $edge     = new Edge(123, null, null, [$property]);

        $actual = $edge->properties;
        self::assertSame($expected, $actual);
    }

    public function testToStringReturnsSummary(): void
    {
        $expected = 'e[123][v[1]-edge-v[3]]';
        $outV     = new Vertex(1, []);
        $inV      = new Vertex(3, []);
        $edge     = new Edge(123, $outV, $inV);

        $actual = (string) $edge;
        self::assertSame($expected, $actual);
    }

    #[DataProvider('equalsProvider')]
    public function testEqualsReturnsEquality(mixed $other, bool $expected): void
    {
        $edge   = new Edge(123, new Vertex(1, []), new Vertex(2, []), [], 'foo');
        $actual = $edge->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        $same      = new Edge(123, new Vertex(1, []), new Vertex(2, []), [], 'bar');
        $different = new Edge(456, new Vertex(1, []), new Vertex(2, []), [], 'foo');

        return [
            'null'      => [null, false],
            'equal'     => [$same, true],
            'not equal' => [$different, false],
        ];
    }
}

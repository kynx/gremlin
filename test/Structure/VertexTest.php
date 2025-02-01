<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure;

use Kynx\Gremlin\Structure\Vertex;
use Kynx\Gremlin\Structure\VertexProperty;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Vertex::class)]
final class VertexTest extends TestCase
{
    public function testConstructorKeysProperties(): void
    {
        $property = new VertexProperty(123, 'foo', 'bar', []);
        $expected = ['foo' => $property];
        $vertex   = new Vertex(456, [$property]);

        $actual = $vertex->properties;
        self::assertSame($expected, $actual);
    }

    public function testToStringReturnsSummary(): void
    {
        $expected = 'v[123]';
        $vertex   = new Vertex(123);

        $actual = (string) $vertex;
        self::assertSame($expected, $actual);
    }

    #[DataProvider('equalsProvider')]
    public function testEqualsReturnsEquality(mixed $other, bool $expected): void
    {
        $vertex = new Vertex(123, [new VertexProperty(1, 'a', 'bar', [])], 'foo');
        $actual = $vertex->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        $same      = new Vertex(123, [new VertexProperty(2, 'b', 'baz', [])], 'bar');
        $different = new Vertex(456, [new VertexProperty(1, 'a', 'bar', [])], 'foo');

        return [
            'null'      => [null, false],
            'equal'     => [$same, true],
            'not equal' => [$different, false],
        ];
    }
}

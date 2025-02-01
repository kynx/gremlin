<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure;

use Kynx\Gremlin\Structure\Property;
use Kynx\Gremlin\Structure\VertexProperty;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(VertexProperty::class)]
final class VertexPropertyTest extends TestCase
{
    public function testConstructorKeysProperties(): void
    {
        $first          = new Property('a', 'foo');
        $second         = new Property('b', 'bar');
        $expected       = ['a' => $first, 'b' => $second];
        $vertexProperty = new VertexProperty(123, 'vp', 'baz', [$first, $second]);

        $actual = $vertexProperty->properties;
        self::assertSame($expected, $actual);
    }

    public function testToStringReturnsSummary(): void
    {
        $expected       = 'vp[foo->bar]';
        $vertexProperty = new VertexProperty(123, 'foo', 'bar', []);

        $actual = (string) $vertexProperty;
        self::assertSame($expected, $actual);
    }

    #[DataProvider('equalsProvider')]
    public function testEqualsReturnsEquality(mixed $other, bool $expected): void
    {
        $vertexProperty = new VertexProperty(123, 'a', 'foo', [new Property('b', 'bar')]);
        $actual         = $vertexProperty->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        $same      = new VertexProperty(123, 'b', 'bar', [new Property('c', 'baz')]);
        $different = new VertexProperty(456, 'a', 'foo', [new Property('b', 'bar')]);

        return [
            'null'      => [null, false],
            'equal'     => [$same, true],
            'not equal' => [$different, false],
        ];
    }
}

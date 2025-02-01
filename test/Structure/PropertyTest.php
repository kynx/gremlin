<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure;

use Kynx\Gremlin\Structure\Property;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(Property::class)]
final class PropertyTest extends TestCase
{
    #[DataProvider('equalsProvider')]
    public function testEqualsReturnsEquality(mixed $other, bool $expected): void
    {
        $property = new Property('foo', 123);
        $actual   = $property->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        return [
            'not property'  => [new stdClass(), false],
            'key differs'   => [new Property('bar', 123), false],
            'value differs' => [new Property('foo', 456), false],
            'value strict'  => [new Property('foo', '123'), false],
            'equals'        => [new Property('foo', 123), true],
        ];
    }

    public function testToStringReturnsSummary(): void
    {
        $expected = 'p[foo->bar]';
        $property = new Property('foo', 'bar');

        $actual = (string) $property;
        self::assertSame($expected, $actual);
    }
}

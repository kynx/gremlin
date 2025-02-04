<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use Kynx\Gremlin\Structure\Type\ShortType;
use Kynx\Gremlin\Structure\Type\TypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(ShortType::class)]
final class ShortTypeTest extends TestCase
{
    #[DataProvider('outOfRangeProvider')]
    public function testConstructorThrowsOutOfRangeException(int $value): void
    {
        self::expectException(TypeException::class);
        self::expectExceptionMessage("Expected value between -32767 and 32767, got $value");
        new ShortType($value);
    }

    public static function outOfRangeProvider(): array
    {
        return [
            'negative' => [-32768],
            'positive' => [32768],
        ];
    }

    public function testGetValueReturnsExpected(): void
    {
        $expected = 123;
        $type     = new ShortType($expected);
        $actual   = $type->getValue();
        self::assertSame($expected, $actual);
    }

    public function testGetSizeReturnsExpected(): void
    {
        $expected = 2;
        $actual   = ShortType::getSize();
        self::assertSame($expected, $actual);
    }

    #[DataProvider('equalsProvider')]
    public function testEquals(?int $value, mixed $other, bool $expected): void
    {
        $type   = new ShortType($value);
        $actual = $type->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        return [
            'null'          => [null, null, false],
            'object'        => [123, new stdClass(), false],
            'zero null'     => [0, new ShortType(null), false],
            'different int' => [123, new ShortType(456), false],
            'null null'     => [null, new ShortType(null), true],
            'int equal'     => [123, new ShortType(123), true],
        ];
    }

    #[DataProvider('toStringProvider')]
    public function testToString(?int $value, string $expected): void
    {
        $type   = new ShortType($value);
        $actual = (string) $type;
        self::assertSame($expected, $actual);
    }

    public static function toStringProvider(): array
    {
        return [
            'null' => [null, ShortType::NULL_STRING],
            'int'  => [32767, '32767'],
        ];
    }
}

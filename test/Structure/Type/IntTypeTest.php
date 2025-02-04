<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use Kynx\Gremlin\Structure\Type\IntType;
use Kynx\Gremlin\Structure\Type\TypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(IntType::class)]
final class IntTypeTest extends TestCase
{
    #[DataProvider('outOfRangeProvider')]
    public function testConstructorThrowsOutOfRangeException(int $value): void
    {
        self::expectException(TypeException::class);
        self::expectExceptionMessage("Expected value between -2147483647 and 2147483647, got $value");
        new IntType($value);
    }

    public static function outOfRangeProvider(): array
    {
        return [
            'negative' => [-2147483648],
            'positive' => [2147483648],
        ];
    }

    public function testGetValueReturnsExpected(): void
    {
        $expected = 123;
        $type     = new IntType($expected);
        $actual   = $type->getValue();
        self::assertSame($expected, $actual);
    }

    public function testGetSizeReturnsExpected(): void
    {
        $expected = 4;
        $actual   = IntType::getSize();
        self::assertSame($expected, $actual);
    }

    #[DataProvider('equalsProvider')]
    public function testEquals(?int $value, mixed $other, bool $expected): void
    {
        $type   = new IntType($value);
        $actual = $type->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        return [
            'null'          => [null, null, false],
            'object'        => [123, new stdClass(), false],
            'zero null'     => [0, new IntType(null), false],
            'different int' => [123, new IntType(456), false],
            'null null'     => [null, new IntType(null), true],
            'int equal'     => [123, new IntType(123), true],
        ];
    }

    #[DataProvider('toStringProvider')]
    public function testToString(?int $value, string $expected): void
    {
        $type   = new IntType($value);
        $actual = (string) $type;
        self::assertSame($expected, $actual);
    }

    public static function toStringProvider(): array
    {
        return [
            'null' => [null, IntType::NULL_STRING],
            'int'  => [123, '123'],
        ];
    }
}

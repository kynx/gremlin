<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use Brick\Math\BigInteger as BrickBigInteger;
use Kynx\Gremlin\Structure\Type\BigIntegerType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(BigIntegerType::class)]
final class BigIntegerTypeTest extends TestCase
{
    public function testGetValue(): void
    {
        $expected = BrickBigInteger::of('12345678901234567890');
        $type     = new BigIntegerType($expected);
        $actual   = $type->getValue();
        self::assertSame($expected, $actual);
    }

    #[DataProvider('equalsProvider')]
    public function testEquals(?BrickBigInteger $bigDecimal, mixed $other, bool $expected): void
    {
        $type = new BigIntegerType($bigDecimal);
        if ($other instanceof BrickBigInteger) {
            $other = new BigIntegerType($other);
        }
        $actual = $type->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        return [
            'int'           => [BrickBigInteger::of(123), 123, false],
            'object'        => [BrickBigInteger::of(123), new stdClass(), false],
            'int null'      => [BrickBigInteger::of(123), new BigIntegerType(null), false],
            'null int'      => [null, BrickBigInteger::of(123), false],
            'int different' => [BrickBigInteger::of(123), BrickBigInteger::of(456), false],
            'int string'    => [BrickBigInteger::of(123), BrickBigInteger::of('123'), true],
            'int equals'    => [BrickBigInteger::of(123), BrickBigInteger::of(123), true],
        ];
    }

    #[DataProvider('toStringProvider')]
    public function testToString(?BrickBigInteger $value, string $expected): void
    {
        $type   = new BigIntegerType($value);
        $actual = (string) $type;
        self::assertSame($expected, $actual);
    }

    public static function toStringProvider(): array
    {
        return [
            'null' => [null, BigIntegerType::NULL_STRING],
            'int'  => [BrickBigInteger::of(123), '123'],
        ];
    }
}

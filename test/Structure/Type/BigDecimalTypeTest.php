<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use Brick\Math\BigDecimal as BrickBigDecimal;
use Kynx\Gremlin\Structure\Type\BigDecimalType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(BigDecimalType::class)]
final class BigDecimalTypeTest extends TestCase
{
    public function testGetValue(): void
    {
        $expected = BrickBigDecimal::of('123456789.01234567890');
        $type     = new BigDecimalType($expected);
        $actual   = $type->getValue();
        self::assertSame($expected, $actual);
    }

    #[DataProvider('equalsProvider')]
    public function testEquals(?BrickBigDecimal $bigDecimal, mixed $other, bool $expected): void
    {
        $type = new BigDecimalType($bigDecimal);
        if ($other instanceof BrickBigDecimal) {
            $other = new BigDecimalType($other);
        }
        $actual = $type->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        return [
            'float'           => [BrickBigDecimal::of(123.4), 123.4, false],
            'object'          => [BrickBigDecimal::of(123.4), new stdClass(), false],
            'null float'      => [null, BrickBigDecimal::of(123.4), false],
            'float null'      => [BrickBigDecimal::of(123.4), new BigDecimalType(null), false],
            'float different' => [BrickBigDecimal::of(123.4), BrickBigDecimal::of(456.7), false],
            'null null'       => [null, new BigDecimalType(null), true],
            'float string'    => [BrickBigDecimal::of(123.4), BrickBigDecimal::of('123.4'), true],
            'float equals'    => [BrickBigDecimal::of(123.4), BrickBigDecimal::of(123.4), true],
        ];
    }

    #[DataProvider('toStringProvider')]
    public function testToString(?BrickBigDecimal $value, string $expected): void
    {
        $type   = new BigDecimalType($value);
        $actual = (string) $type;
        self::assertSame($expected, $actual);
    }

    public static function toStringProvider(): array
    {
        return [
            'null'  => [null, BigDecimalType::NULL_STRING],
            'float' => [BrickBigDecimal::of(123.4), '123.4'],
        ];
    }
}

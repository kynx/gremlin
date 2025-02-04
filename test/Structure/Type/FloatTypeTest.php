<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use Kynx\Gremlin\Structure\Type\FloatType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

use const INF;
use const NAN;
use const PHP_FLOAT_MAX;

#[CoversClass(FloatType::class)]
final class FloatTypeTest extends TestCase
{
    public function testGetValueReturnsExpected(): void
    {
        $expected = 123.45;
        $type     = new FloatType($expected);
        $actual   = $type->getValue();
        self::assertSame($expected, $actual);
    }

    #[DataProvider('equalsProvider')]
    public function testEquals(?float $value, mixed $other, bool $expected): void
    {
        $type   = new FloatType($value);
        $actual = $type->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        return [
            'null'            => [null, null, false],
            'object'          => [123.45, new stdClass(), false],
            'zero null'       => [0, new FloatType(null), false],
            'nan nan'         => [NAN, new FloatType(NAN), false],
            'nan float'       => [NAN, new FloatType(PHP_FLOAT_MAX), false],
            'different float' => [123.46, new FloatType(456.78), false],
            'inf inf'         => [INF, new FloatType(INF), true],
            'null null'       => [null, new FloatType(null), true],
            'float equal'     => [123.45, new FloatType(123.45), true],
        ];
    }

    #[DataProvider('toStringProvider')]
    public function testToString(?float $value, string $expected): void
    {
        $type   = new FloatType($value);
        $actual = (string) $type;
        self::assertSame($expected, $actual);
    }

    public static function toStringProvider(): array
    {
        return [
            'null'  => [null, FloatType::NULL_STRING],
            'float' => [123.45, '123.45'],
        ];
    }
}

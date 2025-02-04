<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use Kynx\Gremlin\Structure\Type\DoubleType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(DoubleType::class)]
final class DoubleTypeTest extends TestCase
{
    public function testGetValueReturnsExpected(): void
    {
        $expected = 123.45;
        $type     = new DoubleType($expected);
        $actual   = $type->getValue();
        self::assertSame($expected, $actual);
    }

    #[DataProvider('equalsProvider')]
    public function testEquals(?float $value, mixed $other, bool $expected): void
    {
        $type   = new DoubleType($value);
        $actual = $type->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        return [
            'null'          => [null, null, false],
            'object'        => [123.45, new stdClass(), false],
            'zero null'     => [0, new DoubleType(null), false],
            'different int' => [123.46, new DoubleType(456.78), false],
            'null null'     => [null, new DoubleType(null), true],
            'int equal'     => [123.45, new DoubleType(123.45), true],
        ];
    }

    #[DataProvider('toStringProvider')]
    public function testToString(?float $value, string $expected): void
    {
        $type   = new DoubleType($value);
        $actual = (string) $type;
        self::assertSame($expected, $actual);
    }

    public static function toStringProvider(): array
    {
        return [
            'null'  => [null, DoubleType::NULL_STRING],
            'float' => [123.45, '123.45'],
        ];
    }
}

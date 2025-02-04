<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use Kynx\Gremlin\Structure\Type\LongType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(LongType::class)]
final class LongTypeTest extends TestCase
{
    public function testGetValueReturnsExpected(): void
    {
        $expected = 9223372036854775807;
        $type     = new LongType($expected);
        $actual   = $type->getValue();
        self::assertSame($expected, $actual);
    }

    public function testGetSizeReturnsExpected(): void
    {
        $expected = 64;
        $actual   = LongType::getSize();
        self::assertSame($expected, $actual);
    }

    #[DataProvider('equalsProvider')]
    public function testEquals(?int $value, mixed $other, bool $expected): void
    {
        $type   = new LongType($value);
        $actual = $type->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        return [
            'null'          => [null, null, false],
            'object'        => [123, new stdClass(), false],
            'zero null'     => [0, new LongType(null), false],
            'different int' => [123, new LongType(456), false],
            'null null'     => [null, new LongType(null), true],
            'int equal'     => [123, new LongType(123), true],
        ];
    }

    #[DataProvider('toStringProvider')]
    public function testToString(?int $value, string $expected): void
    {
        $type   = new LongType($value);
        $actual = (string) $type;
        self::assertSame($expected, $actual);
    }

    public static function toStringProvider(): array
    {
        return [
            'null' => [null, LongType::NULL_STRING],
            'int'  => [123, '123'],
        ];
    }
}

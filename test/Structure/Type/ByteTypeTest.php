<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use Kynx\Gremlin\Structure\Type\ByteType;
use Kynx\Gremlin\Structure\Type\TypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(ByteType::class)]
final class ByteTypeTest extends TestCase
{
    #[DataProvider('outOfRangeProvider')]
    public function testConstructorThrowsOutOfRangeException(int $value): void
    {
        self::expectException(TypeException::class);
        self::expectExceptionMessage("Expected value between 0 and 255, got $value");
        new ByteType($value);
    }

    public static function outOfRangeProvider(): array
    {
        return [
            '256' => [256],
            '-1'  => [-1],
        ];
    }

    public function testGetValueReturnsExpected(): void
    {
        $expected = 127;
        $type     = new ByteType($expected);
        $actual   = $type->getValue();
        self::assertSame($expected, $actual);
    }

    #[DataProvider('equalsProvider')]
    public function testEquals(?int $value, mixed $other, bool $expected): void
    {
        $type   = new ByteType($value);
        $actual = $type->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        return [
            'null'        => [null, null, false],
            'object'      => [null, new stdClass(), false],
            'null byte'   => [null, new ByteType(0), false],
            'byte null'   => [0, new ByteType(null), false],
            'null equals' => [null, new ByteType(null), true],
            'byte equals' => [255, new ByteType(255), true],
        ];
    }

    #[DataProvider('toStringProvider')]
    public function testToString(?int $value, string $expected): void
    {
        $type   = new ByteType($value);
        $actual = (string) $type;
        self::assertSame($expected, $actual);
    }

    public static function toStringProvider(): array
    {
        return [
            'null' => [null, ByteType::NULL_STRING],
            '255'  => [255, '255'],
        ];
    }
}

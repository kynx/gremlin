<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use Kynx\Gremlin\Structure\Type\CharType;
use Kynx\Gremlin\Structure\Type\TypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(CharType::class)]
final class CharTypeTest extends TestCase
{
    #[DataProvider('invalidValueProvider')]
    public function testConstructorValidatesValue(string $value): void
    {
        self::expectException(TypeException::class);
        self::expectExceptionMessage("Expected single character UTF-8 string, got: '$value'");
        new CharType($value);
    }

    public static function invalidValueProvider(): array
    {
        return [
            "empty string"    => [""],
            "multi-character" => ["ab"],
            "not utf-8"       => ["\xc0"],
        ];
    }

    #[DataProvider('expectedValueProvider')]
    public function testGetValueReturnsExpected(?string $expected): void
    {
        $type   = new CharType($expected);
        $actual = $type->getValue();
        self::assertSame($expected, $actual);
    }

    public static function expectedValueProvider(): array
    {
        return [
            'null'       => [null],
            'char'       => ['a'],
            'multi-byte' => ["€"],
        ];
    }

    #[DataProvider('equalsProvider')]
    public function testEquals(?string $value, mixed $other, bool $expected): void
    {
        $type   = new CharType($value);
        $actual = $type->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        return [
            'null'          => [null, null, false],
            'object'        => ["a", new stdClass(), false],
            "null 0"        => [null, new CharType("0"), false],
            "null equals"   => [null, new CharType(null), true],
            "string equals" => ["a", new CharType("a"), true],
        ];
    }

    #[DataProvider('toStringProvider')]
    public function testToStringReturnsExpected(?string $value, string $expected): void
    {
        $type   = new CharType($value);
        $actual = (string) $type;
        self::assertSame($expected, $actual);
    }

    public static function toStringProvider(): array
    {
        return [
            'null'   => [null, CharType::NULL_STRING],
            "string" => ["€", "€"],
        ];
    }
}

<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use Kynx\Gremlin\Structure\Type\StringType;
use Kynx\Gremlin\Structure\Type\TypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

use function str_repeat;

#[CoversClass(StringType::class)]
final class StringTypeTest extends TestCase
{
    public function testConstructorThrowsTypeException(): void
    {
        $value = "\xc0\x00";
        self::expectException(TypeException::class);
        self::expectExceptionMessage("Expected UTF-8 string, got: '$value'");
        new StringType($value);
    }

    #[DataProvider('getValueProvider')]
    public function testGetValueReturnsExpected(?string $expected): void
    {
        $type   = new StringType($expected);
        $actual = $type->getValue();
        self::assertSame($expected, $actual);
    }

    public static function getValueProvider(): array
    {
        return [
            'null'              => [null],
            'empty'             => [''],
            'string'            => ['foo'],
            'long with invalid' => [str_repeat('a', 1024) . "\xc0\x00"],
        ];
    }

    #[DataProvider('equalsProvider')]
    public function testEquals(?string $value, mixed $other, bool $expected): void
    {
        $type   = new StringType($value);
        $actual = $type->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        return [
            'null'       => [null, null, false],
            'object'     => [null, new stdClass(), false],
            'null empty' => [null, new StringType(''), false],
            'not equal'  => ['a', new StringType('b'), false],
            'null equal' => [null, new StringType(null), true],
            'equal'      => ['a', new StringType('a'), true],
        ];
    }

    #[DataProvider('toStringProvider')]
    public function testToStringReturnsExpected(?string $value, string $expected): void
    {
        $type   = new StringType($value);
        $actual = (string) $type;
        self::assertSame($expected, $actual);
    }

    public static function toStringProvider(): array
    {
        return [
            'null'   => [null, StringType::NULL_STRING],
            'string' => ['a', 'a'],
        ];
    }
}

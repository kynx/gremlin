<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use Kynx\Gremlin\Structure\Type\TypeException;
use Kynx\Gremlin\Structure\Type\UuidType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(UuidType::class)]
final class UuidTypeTest extends TestCase
{
    public function testConstructorValidatesString(): void
    {
        $format = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';
        $value  = 'foo';
        self::expectException(TypeException::class);
        self::expectExceptionMessage("Expected string in format '$format', got: '$value'");
        new UuidType($value);
    }

    #[DataProvider('getValueProvider')]
    public function testGetValueReturnsExpected(?string $expected): void
    {
        $type   = new UuidType($expected);
        $actual = $type->getValue();
        self::assertSame($expected, $actual);
    }

    public static function getValueProvider(): array
    {
        return [
            'null' => [null],
            'uuid' => ['00112233-4455-6677-8899-aabbccddeeff'],
        ];
    }

    #[DataProvider('equalsProvider')]
    public function testEquals(?string $value, mixed $other, bool $expected): void
    {
        $type   = new UuidType($value);
        $actual = $type->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'null'        => [null, null, false],
            'object'      => [null, new stdClass(), false],
            'string null' => ['00112233-4455-6677-8899-aabbccddeeff', new UuidType(null), false],
            'different'   => ['00112233-4455-6677-8899-aabbccddeeff', new UuidType('aa112233-4455-6677-8899-aabbccddeeff'), false],
            'null null'   => [null, new UuidType(null), true],
            'equals'      => ['00112233-4455-6677-8899-aabbccddeeff', new UuidType('00112233-4455-6677-8899-aabbccddeeff'), true],
        ];
        // phpcs:enable
    }

    #[DataProvider('toStringProvider')]
    public function testToString(?string $value, string $expected): void
    {
        $type   = new UuidType($value);
        $actual = (string) $type;
        self::assertSame($expected, $actual);
    }

    public static function toStringProvider(): array
    {
        return [
            'null' => [null, UuidType::NULL_STRING],
            'uuid' => ['00112233-4455-6677-8899-aabbccddeeff', '00112233-4455-6677-8899-aabbccddeeff'],
        ];
    }
}

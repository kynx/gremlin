<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use Kynx\Gremlin\Structure\Type\BooleanType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(BooleanType::class)]
final class BooleanTypeTest extends TestCase
{
    public function testGetValue(): void
    {
        $expected = true;
        $boolean  = new BooleanType($expected);
        $actual   = $boolean->getValue();
        self::assertSame($expected, $actual);
    }

    #[DataProvider('equalsProvider')]
    public function testEquals(BooleanType $boolean, mixed $other, bool $expected)
    {
        $actual = $boolean->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        return [
            'scalar'     => [new BooleanType(true), "foo", false],
            'object'     => [new BooleanType(true), new stdClass(), false],
            'true false' => [new BooleanType(true), new BooleanType(false), false],
            'null false' => [new BooleanType(null), new BooleanType(false), false],
            'null'       => [new BooleanType(null), new BooleanType(null), true],
            'true'       => [new BooleanType(true), new BooleanType(true), true],
            'false'      => [new BooleanType(false), new BooleanType(false), true],
        ];
    }

    #[DataProvider('toStringProvider')]
    public function testToString(?bool $value, string $expected): void
    {
        $boolean = new BooleanType($value);
        $actual  = (string) $boolean;
        self::assertSame($expected, $actual);
    }

    public static function toStringProvider(): array
    {
        return [
            'null'  => [null, BooleanType::NULL_STRING],
            'true'  => [true, 'true'],
            'false' => [false, 'false'],
        ];
    }
}

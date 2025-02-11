<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use Kynx\Gremlin\Structure\Type\IntType;
use Kynx\Gremlin\Structure\Type\UnspecifiedNullObject;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnspecifiedNullObject::class)]
final class UnspecifiedNullObjectTest extends TestCase
{
    public function testGetValueReturnsNull(): void
    {
        $type   = new UnspecifiedNullObject();
        $actual = $type->getValue();
        self::assertNull($actual);
    }

    #[DataProvider('equalsProvider')]
    public function testEquals(mixed $other, bool $expected): void
    {
        $type   = new UnspecifiedNullObject();
        $actual = $type->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        return [
            'null'      => [null, false],
            'null type' => [new IntType(null), false],
            'equals'    => [new UnspecifiedNullObject(), true],
        ];
    }

    public function testToStringReturnsNullString(): void
    {
        $type   = new UnspecifiedNullObject();
        $actual = (string) $type;
        self::assertSame(UnspecifiedNullObject::NULL_STRING, $actual);
    }
}

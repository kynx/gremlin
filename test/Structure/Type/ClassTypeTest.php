<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use Kynx\Gremlin\Structure\Type\ClassType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(ClassType::class)]
final class ClassTypeTest extends TestCase
{
    #[DataProvider('getValueProvider')]
    public function testGetValueReturnsExpected(?string $expected): void
    {
        $type   = new ClassType($expected);
        $actual = $type->getValue();
        self::assertSame($expected, $actual);
    }

    public static function getValueProvider(): array
    {
        return [
            'null'   => [null],
            'empty'  => [''],
            'string' => ['java.io.File'],
        ];
    }

    #[DataProvider('equalsProvider')]
    public function testEquals(?string $value, mixed $other, bool $expected): void
    {
        $type   = new ClassType($value);
        $actual = $type->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        return [
            'null'       => [null, null, false],
            'object'     => [null, new stdClass(), false],
            'null empty' => [null, new ClassType(''), false],
            'not equal'  => ['java.io.File', new ClassType('java.io.Directory'), false],
            'null equal' => [null, new ClassType(null), true],
            'equal'      => ['java.io.File', new ClassType('java.io.File'), true],
        ];
    }

    #[DataProvider('toStringProvider')]
    public function testToStringReturnsExpected(?string $value, string $expected): void
    {
        $type   = new ClassType($value);
        $actual = (string) $type;
        self::assertSame($expected, $actual);
    }

    public static function toStringProvider(): array
    {
        return [
            'null'   => [null, ClassType::NULL_STRING],
            'string' => ['java.io.File', 'java.io.File'],
        ];
    }
}

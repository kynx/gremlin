<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use ArrayIterator;
use Generator;
use Kynx\Gremlin\Structure\Type\IntType;
use Kynx\Gremlin\Structure\Type\SetType;
use Kynx\Gremlin\Structure\Type\TypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

use function array_map;

/**
 * @psalm-import-type ValuesType from SetType
 */
#[CoversClass(SetType::class)]
final class SetTypeTest extends TestCase
{
    public function testConstructorThrowsExceptionWhenPassedGenerator(): void
    {
        $generator = static function (): Generator {
            yield new IntType(1);
        };

        self::expectException(TypeException::class);
        self::expectExceptionMessage(SetType::class . " does not accept values of type 'Generator'");
        new SetType($generator());
    }

    public function testConstructorThrowExceptionWhenValuesNotUnique(): void
    {
        $values = $this->items(1, 1, 2, 3);

        self::expectException(TypeException::class);
        self::expectExceptionMessage("Duplicate value '1' found in set");
        new SetType($values);
    }

    /**
     * @param ValuesType $expected
     */
    #[DataProvider('getValueProvider')]
    public function testGetValueReturnsExpected(?iterable $expected): void
    {
        $type   = new SetType($expected);
        $actual = $type->getValue();
        self::assertSame($expected, $actual);
    }

    public static function getValueProvider(): array
    {
        return [
            'null'     => [null],
            'array'    => [self::items(3, 2, 1)],
            'iterator' => [new ArrayIterator(self::items(3, 2, 1))],
        ];
    }

    /**
     * @param ValuesType $value
     */
    #[DataProvider('lengthProvider')]
    public function testGetLengthReturnsExpected(?iterable $value, int $expected): void
    {
        $type   = new SetType($value);
        $actual = $type->getLength();
        self::assertSame($expected, $actual);
    }

    public static function lengthProvider(): array
    {
        return [
            'null'     => [null, 0],
            'array'    => [self::items(1), 1],
            'iterator' => [new ArrayIterator(self::items(1)), 1],
        ];
    }

    /**
     * @param ValuesType $value
     */
    #[DataProvider('equalsProvider')]
    public function testEquals(?iterable $value, mixed $other, bool $expected): void
    {
        $type   = new SetType($value);
        $actual = $type->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        return [
            'null'             => [null, null, false],
            'object'           => [null, new stdClass(), false],
            'null empty'       => [null, new SetType([]), false],
            'empty null'       => [[], new SetType(null), false],
            'different length' => [self::items(1, 2), new SetType(self::items(1)), false],
            'different order'  => [self::items(1, 2), new SetType(self::items(2, 1)), false],
            'not list'         => [self::items(1), new SetType(self::items(a: 1)), false],
            'both null'        => [null, new SetType(null), true],
            'both empty'       => [[], new SetType([]), true],
            'equals'           => [self::items(1, 2), new SetType(self::items(1, 2)), true],
        ];
    }

    /**
     * @param ValuesType $items
     */
    #[DataProvider('toStringProvider')]
    public function testToString(?iterable $items, string $expected): void
    {
        $type   = new SetType($items);
        $actual = (string) $type;
        self::assertSame($expected, $actual);
    }

    public static function toStringProvider(): array
    {
        return [
            'null' => [null, SetType::NULL_STRING],
            'list' => [self::items(1, 2), 'Set(2)'],
        ];
    }

    /**
     * @return array<IntType>
     */
    private static function items(int ...$values): array
    {
        return array_map(static fn (int $i): IntType => new IntType($i), $values);
    }
}

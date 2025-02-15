<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use ArrayIterator;
use Generator;
use Kynx\Gremlin\Structure\Type\IntType;
use Kynx\Gremlin\Structure\Type\ListType;
use Kynx\Gremlin\Structure\Type\TypeException;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

use function array_map;

/**
 * @psalm-import-type ValuesType from ListType
 */
#[CoversClass(ListType::class)]
final class ListTypeTest extends TestCase
{
    public function testConstructorThrowsExceptionWhenLengthNotSpecified(): void
    {
        $expected  = 'Length is required for type ' . ListType::class . " with value of: " . Generator::class;
        $generator = static function (): Generator {
            yield new IntType(1);
        };

        self::expectException(TypeException::class);
        self::expectExceptionMessage($expected);
        new ListType($generator());
    }

    /**
     * @param ValuesType $expected
     */
    #[DataProvider('getValueProvider')]
    public function testGetValueReturnsExpected(?iterable $expected): void
    {
        $type   = new ListType($expected);
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
    public function testGetLengthReturnsExpected(?iterable $value, ?int $length, int $expected): void
    {
        $type   = new ListType($value, $length);
        $actual = $type->getLength();
        self::assertSame($expected, $actual);
    }

    public static function lengthProvider(): array
    {
        $generator = static function (): Generator {
            yield new IntType(1);
        };

        return [
            'null'            => [null, null, 0],
            'array'           => [self::items(1), null, 1],
            'iterator'        => [new ArrayIterator(self::items(1)), null, 1],
            'override length' => [self::items(1, 2), 1, 1],
            'generator'       => [$generator(), 1, 1],
        ];
    }

    /**
     * @param ValuesType $value
     */
    #[DataProvider('equalsProvider')]
    public function testEquals(?iterable $value, mixed $other, bool $expected): void
    {
        $length = $value instanceof Generator ? 1 : null;
        $type   = new ListType($value, $length);
        $actual = $type->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        $generator = static function (): Generator {
            yield new IntType(1);
        };

        return [
            'null'             => [null, null, false],
            'object'           => [null, new stdClass(), false],
            'null empty'       => [null, new ListType([]), false],
            'empty null'       => [[], new ListType(null), false],
            'different length' => [self::items(1, 2), new ListType(self::items(1)), false],
            'different order'  => [self::items(1, 2), new ListType(self::items(2, 1)), false],
            'not list'         => [self::items(1), new ListType(self::items(a: 1)), false],
            'generator list'   => [$generator(), new ListType(self::items(1)), false],
            'list generator'   => [self::items(1), new ListType($generator(), 1), false],
            'both null'        => [null, new ListType(null), true],
            'both empty'       => [[], new ListType([]), true],
            'same values'      => [self::items(1, 2), new ListType(self::items(1, 2)), true],
        ];
    }

    /**
     * @param array<TypeInterface>|null $items
     */
    #[DataProvider('toStringProvider')]
    public function testToString(?array $items, string $expected): void
    {
        $type   = new ListType($items);
        $actual = (string) $type;
        self::assertSame($expected, $actual);
    }

    public static function toStringProvider(): array
    {
        return [
            'null' => [null, ListType::NULL_STRING],
            'list' => [self::items(1, 2), 'List(2)'],
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

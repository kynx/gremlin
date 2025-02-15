<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use ArrayIterator;
use Generator;
use Kynx\Gremlin\Structure\Type\IntType;
use Kynx\Gremlin\Structure\Type\MapItem;
use Kynx\Gremlin\Structure\Type\MapType;
use Kynx\Gremlin\Structure\Type\StringType;
use Kynx\Gremlin\Structure\Type\TypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

use function is_int;

/**
 * @psalm-import-type ValuesType from MapType
 */
#[CoversClass(MapType::class)]
final class MapTypeTest extends TestCase
{
    public function testConstructorThrowsExceptionWhenLengthNotSpecified(): void
    {
        $expected  = 'Length is required for type ' . MapType::class . " with value of: " . Generator::class;
        $generator = static function (): Generator {
            yield new IntType(1);
        };

        self::expectException(TypeException::class);
        self::expectExceptionMessage($expected);
        /** @psalm-suppress InvalidArgument That's what we're testing */
        new MapType($generator());
    }

    /**
     * @param ValuesType $expected
     */
    #[DataProvider('getValueProvider')]
    public function testGetValueReturnsExpected(iterable|null $expected): void
    {
        $type   = new MapType($expected);
        $actual = $type->getValue();
        self::assertSame($expected, $actual);
    }

    public static function getValueProvider(): array
    {
        return [
            'null'     => [null],
            'array'    => [self::items(a: 1, b: 2)],
            'iterator' => [new ArrayIterator(self::items(a: 1, b: 2))],
        ];
    }

    /**
     * @param ValuesType $value
     */
    #[DataProvider('lengthProvider')]
    public function testGetLengthReturnsExpected(?iterable $value, ?int $length, int $expected): void
    {
        $type   = new MapType($value, $length);
        $actual = $type->getLength();
        self::assertSame($expected, $actual);
    }

    public static function lengthProvider(): array
    {
        $generator = static function (): Generator {
            yield 'a' => new IntType(1);
        };

        return [
            'null'            => [null, null, 0],
            'array'           => [self::items(a: 1), null, 1],
            'iterator'        => [new ArrayIterator(self::items(a: 1)), null, 1],
            'override length' => [self::items(a: 1, b: 2), 1, 1],
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
        $type   = new MapType($value, $length);
        $actual = $type->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        $generator = static function (): Generator {
            yield 'a' => new MapItem(new StringType('foo'), new IntType(1));
        };

        return [
            'null'             => [null, null, false],
            'object'           => [null, new stdClass(), false],
            'null empty'       => [null, new MapType([]), false],
            'empty null'       => [[], new MapType(null), false],
            'different length' => [self::items(a: 1, b: 2), new MapType(self::items(a: 1)), false],
            'different keys'   => [self::items(a: 1, b: 2), new MapType(self::items(a: 1, c: 2)), false],
            'generator list'   => [$generator(), new MapType(self::items(a: 1)), false],
            'list generator'   => [self::items(a: 1), new MapType($generator(), 1), false],
            'both null'        => [null, new MapType(null), true],
            'both empty'       => [[], new MapType([]), true],
            'int keys'         => [self::items(1, 2), new MapType(self::items(1, 2)), true],
        ];
    }

    /**
     * @param ValuesType $items
     */
    #[DataProvider('toStringProvider')]
    public function testToString(?iterable $items, string $expected): void
    {
        $type   = new MapType($items);
        $actual = (string) $type;
        self::assertSame($expected, $actual);
    }

    public static function toStringProvider(): array
    {
        return [
            'null' => [null, MapType::NULL_STRING],
            'list' => [self::items(a:1, b: 2), 'Map(2)'],
        ];
    }

    /**
     * @return list<MapItem>
     */
    private static function items(int|string ...$values): array
    {
        $items = [];

        /**
         * @var int|string $k
         * @var int $v
         */
        foreach ($values as $k => $v) {
            $key     = is_int($k) ? new IntType($k) : new StringType($k);
            $items[] = new MapItem($key, new IntType($v));
        }

        return $items;
    }
}

<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure;

use Kynx\Gremlin\Structure\Util;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Stringable;

use function str_repeat;

#[CoversClass(Util::class)]
final class UtilTest extends TestCase
{
    #[DataProvider('toStringProvider')]
    public function testToStringAcceptsValue(mixed $value, string $expected): void
    {
        $actual = Util::toString($value);
        self::assertSame($expected, $actual);
    }

    public static function toStringProvider(): array
    {
        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return 'stringable';
            }
        };

        return [
            'null'        => [null, '`null`'],
            'true'        => [true, '`true`'],
            'false'       => [false, '`false`'],
            'int'         => [123, '123'],
            'float'       => [1.23, '1.23'],
            'string'      => ['abc', 'abc'],
            'stringable'  => [$stringable, 'stringable'],
            'long string' => [str_repeat('a', 21), str_repeat('a', 17) . '...'],
            'object'      => [new stdClass(), 'stdClass'],
        ];
    }
}

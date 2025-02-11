<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Kynx\Gremlin\Structure\Type\DateType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(DateType::class)]
final class DateTypeTest extends TestCase
{
    #[DataProvider('getValueProvider')]
    public function testGetValueReturnsExpected(?DateTimeInterface $expected): void
    {
        $type   = new DateType($expected);
        $actual = $type->getValue();
        self::assertSame($expected, $actual);
    }

    public static function getValueProvider(): array
    {
        return [
            'null'              => [null],
            'datetime'          => [new DateTime('2025-02-11T15:23:00.000000')],
            'datetimeimmutable' => [new DateTimeImmutable('2025-02-11T15:23:00.000000')],
        ];
    }

    #[DataProvider('equalsProvider')]
    public function testEquals(?DateTimeInterface $value, mixed $other, bool $expected): void
    {
        $type   = new DateType($value);
        $actual = $type->equals($other);
        self::assertSame($expected, $actual);
    }

    public static function equalsProvider(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'null'           => [null, null, false],
            'object'         => [null, new stdClass(), false],
            'null zero date' => [null, new DateType(new DateTimeImmutable('0000-00-00T00:00:00.000000')), false],
            'different date' => [new DateTimeImmutable('tomorrow'), new DateType(new DateTimeImmutable('yesterday')), false],
            'null null'      => [null, new DateType(null), true],
            'equals'         => [new DateTime('2025-02-11T15:23:00.000000'), new DateType(new DateTime('2025-02-11T15:23:00.000000')), true],
        ];
        // phpcs:enable
    }

    #[DataProvider('toStringProvider')]
    public function testToString(?DateTimeInterface $value, string $expected): void
    {
        $type   = new DateType($value);
        $actual = (string) $type;
        self::assertSame($expected, $actual);
    }

    public static function toStringProvider(): array
    {
        return [
            'null' => [null, DateType::NULL_STRING],
            'date' => [new DateTimeImmutable('2025-02-11T15:23:00.000000'), '2025-02-11T15:23:00.000+00:00'],
        ];
    }
}

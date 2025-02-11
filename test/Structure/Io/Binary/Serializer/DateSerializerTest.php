<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use DateTime;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\DateSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Type\DateType;
use Kynx\Gremlin\Structure\Type\StringType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DateSerializer::class)]
final class DateSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): SerializerInterface
    {
        return new DateSerializer();
    }

    public static function serializableTypesProvider(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'null'                => [new DateType(null), "\x01"],
            'unix epoc'           => [new DateType(new DateTime('1970-01-01T00:00:00.000Z')), "\x00\x00\x00\x00\x00\x00\x00\x00\x00"],
            'plus a microsecond'  => [new DateType(new DateTime('1970-01-01T00:00:00.001Z')), "\x00\x00\x00\x00\x00\x00\x00\x00\x01"],
            'minus a microsecond' => [new DateType(new DateTime('1969-12-31T23:59:59.999Z')), "\x00\xff\xff\xff\xff\xff\xff\xff\xff"],
        ];
        // phpcs:enable
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'string' => [new StringType(null)],
        ];
    }
}

<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\TimestampSerializer;
use Kynx\Gremlin\Structure\Type\IntType;
use Kynx\Gremlin\Structure\Type\TimestampType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TimestampSerializer::class)]
final class TimestampSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): SerializerInterface
    {
        return new TimestampSerializer();
    }

    public static function serializableTypesProvider(): array
    {
        return [
            'null'          => [new TimestampType(null), "\x01"],
            'timestamp 0'   => [new TimestampType(0), "\x00\x00\x00\x00\x00\x00\x00\x00\x00"],
            'timestamp 1'   => [new TimestampType(1), "\x00\x00\x00\x00\x00\x00\x00\x00\x01"],
            'timestamp max' => [new TimestampType(9223372036854775807), "\x00\x7f\xff\xff\xff\xff\xff\xff\xff"],
            'timestamp -1'  => [new TimestampType(-1), "\x00\xff\xff\xff\xff\xff\xff\xff\xff"],
            'timestamp -2'  => [new TimestampType(-2), "\x00\xff\xff\xff\xff\xff\xff\xff\xfe"],
            'timestamp min' => [new TimestampType(-9223372036854775807), "\x00\x80\x00\x00\x00\x00\x00\x00\x01"],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'int' => [new IntType(null)],
        ];
    }
}

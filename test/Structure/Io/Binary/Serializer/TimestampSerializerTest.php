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
            'timestamp null' => [new TimestampType(null), "\x01"],
            'timestamp -2'   => [new TimestampType(-2), "\x00\xff\xff\xff\xff\xff\xff\xff\xfe"],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'int' => [new IntType(null)],
        ];
    }
}

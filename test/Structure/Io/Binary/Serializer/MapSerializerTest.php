<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\IntSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\MapSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\StringSerializer;
use Kynx\Gremlin\Structure\Type\IntType;
use Kynx\Gremlin\Structure\Type\ListType;
use Kynx\Gremlin\Structure\Type\MapItem;
use Kynx\Gremlin\Structure\Type\MapType;
use Kynx\Gremlin\Structure\Type\StringType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MapSerializer::class)]
final class MapSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): MapSerializer
    {
        return new MapSerializer();
    }

    protected function getSerializers(): array
    {
        return [
            new IntSerializer(),
            new StringSerializer(),
        ];
    }

    public static function serializableTypesProvider(): array
    {
        return [
            'map null'     => [new MapType(null), "\x01"],
            'map empty'    => [new MapType([]), "\x00\x00\x00\x00\x00"],
            'map one item' => [
                new MapType([new MapItem(new StringType('foo'), new IntType(42))]),
                "\x00" // flag
                . "\x00\x00\x00\x01" // length 1
                . "\x03\x00\x00\x00\x00\x03\x66\x6f\x6f" // string 'foo'
                . "\x01\x00\x00\x00\x00\x2a", // int 42
            ],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'map list' => [new ListType(null)],
        ];
    }
}

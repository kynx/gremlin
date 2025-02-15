<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\BooleanSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\ByteSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\IntSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\ListSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\ShortSerializer;
use Kynx\Gremlin\Structure\Type\BooleanType;
use Kynx\Gremlin\Structure\Type\ByteType;
use Kynx\Gremlin\Structure\Type\DateType;
use Kynx\Gremlin\Structure\Type\IntType;
use Kynx\Gremlin\Structure\Type\ListType;
use Kynx\Gremlin\Structure\Type\ShortType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ListSerializer::class)]
final class ListSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): ListSerializer
    {
        return new ListSerializer();
    }

    protected function getSerializers(): array
    {
        return [
            new BooleanSerializer(),
            new ByteSerializer(),
            new IntSerializer(),
            new ShortSerializer(),
        ];
    }

    public static function serializableTypesProvider(): array
    {
        // phpcs:disable Generic.Strings.UnnecessaryStringConcat.Found
        return [
            'list null'        => [new ListType(null), "\x01"],
            'list empty'       => [new ListType([]), "\x00\x00\x00\x00\x00"],
            'list single int'  => [
                new ListType([new IntType(1)]),
                // flag  length               int 1
                "\x00" . "\x00\x00\x00\x01" . "\x01\x00\x00\x00\x00\x01",
            ],
            'list three items' => [
                new ListType([new ByteType(42), new ShortType(42), new BooleanType(true)]),
                // flag  length               byte             short                boolean
                "\x00" . "\x00\x00\x00\x03" . "\x24\x00\x2a" . "\x26\x00\x00\x2a" . "\x27\x00\x01",
            ],
        ];
        // phpcs:enable
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'date' => [new DateType(null)],
        ];
    }
}

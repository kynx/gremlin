<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\IntSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SetSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\StringSerializer;
use Kynx\Gremlin\Structure\Type\IntType;
use Kynx\Gremlin\Structure\Type\ListType;
use Kynx\Gremlin\Structure\Type\SetType;
use Kynx\Gremlin\Structure\Type\StringType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SetSerializer::class)]
final class SetSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): SetSerializer
    {
        return new SetSerializer();
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
        // phpcs:disable Generic.Strings.UnnecessaryStringConcat.Found
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'set null'          => [new SetType(null), "\x01"],
            'set empty'         => [new SetType([]), "\x00\x00\x00\x00\x00"],
            'set single int'    => [
                new SetType([new IntType(1)]),
                // flag  length               int 1
                "\x00" . "\x00\x00\x00\x01" . "\x01\x00\x00\x00\x00\x01",
            ],
            'set three strings' => [
                new SetType([new StringType('b'), new StringType('a'), new StringType('c')]),
                // flag  length               string b                         string a                         string c
                "\x00" . "\x00\x00\x00\x03" . "\x03\x00\x00\x00\x00\x01\x62" . "\x03\x00\x00\x00\x00\x01\x61" . "\x03\x00\x00\x00\x00\x01\x63",
            ],
        ];
        // phpcs:enable
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'set list' => [new ListType(null)],
        ];
    }
}

<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\CharSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Type\ByteType;
use Kynx\Gremlin\Structure\Type\CharType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CharSerializer::class)]
final class CharSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): SerializerInterface
    {
        return new CharSerializer();
    }

    public static function serializableTypesProvider(): array
    {
        return [
            'null'        => [new CharType(null), "\x01"],
            'single byte' => [new CharType("~"), "\x00\x7e"],
            'two bytes'   => [new CharType("¡"), "\x00\xc2\xa1"],
            "three bytes" => [new CharType("ࠀ"), "\x00\xe0\xa0\x80"],
            "four bytes"  => [new CharType("𐊀"), "\x00\xf0\x90\x8a\x80"],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            "byte" => [new ByteType(null)],
        ];
    }
}

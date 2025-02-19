<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\StringSerializer;
use Kynx\Gremlin\Structure\Type\CharType;
use Kynx\Gremlin\Structure\Type\StringType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(StringSerializer::class)]
final class StringSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): SerializerInterface
    {
        return new StringSerializer();
    }

    public static function serializableTypesProvider(): array
    {
        return [
            'string null'        => [new StringType(null), "\x01"],
            'string empty'       => [new StringType(''), "\x00\x00\x00\x00\x00"],
            'string single byte' => [new StringType("abc"), "\x00\x00\x00\x00\x03\x61\x62\x63"],
            'string multi-byte'  => [new StringType('ࠀ'), "\x00\x00\x00\x00\x03\xe0\xa0\x80"],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'char' => [new CharType(null)],
        ];
    }
}

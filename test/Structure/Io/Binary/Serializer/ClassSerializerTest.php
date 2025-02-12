<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\ClassSerializer;
use Kynx\Gremlin\Structure\Type\ClassType;
use Kynx\Gremlin\Structure\Type\StringType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ClassSerializer::class)]
final class ClassSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): ClassSerializer
    {
        return new ClassSerializer();
    }

    public static function serializableTypesProvider(): array
    {
        return [
            'class null'               => [new ClassType(null), "\x01"],
            'class empty'              => [new ClassType(''), "\x00\x00\x00\x00\x00"],
            'class single byte string' => [new ClassType("abc"), "\x00\x00\x00\x00\x03\x61\x62\x63"],
            'class multi-byte string'  => [new ClassType('ࠀ'), "\x00\x00\x00\x00\x03\xe0\xa0\x80"],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'string' => [new StringType(null)],
        ];
    }
}

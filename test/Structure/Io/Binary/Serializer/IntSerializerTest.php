<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\IntSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Type\BooleanType;
use Kynx\Gremlin\Structure\Type\IntType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(IntSerializer::class)]
final class IntSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): SerializerInterface
    {
        return new IntSerializer();
    }

    public static function serializableTypesProvider(): array
    {
        return [
            'null'    => [new IntType(null), "\x01"],
            'int 0'   => [new IntType(0), "\x00\x00\x00\x00\x00"],
            'int 1'   => [new IntType(1), "\x00\x00\x00\x00\x01"],
            'int 256' => [new IntType(256), "\x00\x00\x00\x01\x00"],
            'int max' => [new IntType(2147483647), "\x00\x7f\xff\xff\xff"],
            'int -1'  => [new IntType(-1), "\x00\xff\xff\xff\xff"],
            'int -2'  => [new IntType(-2), "\x00\xff\xff\xff\xfe"],
            'int min' => [new IntType(-2147483647), "\x00\x80\x00\x00\x01"],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'boolean' => [new BooleanType(null)],
        ];
    }
}

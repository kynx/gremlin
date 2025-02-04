<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\ShortSerializer;
use Kynx\Gremlin\Structure\Type\IntType;
use Kynx\Gremlin\Structure\Type\ShortType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ShortSerializer::class)]
final class ShortSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): SerializerInterface
    {
        return new ShortSerializer();
    }

    public static function serializableTypesProvider(): array
    {
        return [
            'null'    => [new ShortType(null), "\x01"],
            'int 0'   => [new ShortType(0), "\x00\x00\x00"],
            'int 1'   => [new ShortType(1), "\x00\x00\x01"],
            'int 256' => [new ShortType(256), "\x00\x01\x00"],
            'int max' => [new ShortType(32767), "\x00\x7f\xff"],
            'int -1'  => [new ShortType(-1), "\x00\xff\xff"],
            'int -2'  => [new ShortType(-2), "\x00\xff\xfe"],
            'int min' => [new ShortType(-32767), "\x00\x80\x01"],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'int' => [new IntType(255)],
        ];
    }
}

<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\LongSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Type\IntType;
use Kynx\Gremlin\Structure\Type\LongType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LongSerializer::class)]
final class LongSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): SerializerInterface
    {
        return new LongSerializer();
    }

    public static function serializableTypesProvider(): array
    {
        return [
            'null'     => [new LongType(null), "\x01"],
            'long 0'   => [new LongType(0), "\x00\x00\x00\x00\x00\x00\x00\x00\x00"],
            'long 1'   => [new LongType(1), "\x00\x00\x00\x00\x00\x00\x00\x00\x01"],
            'long max' => [new LongType(9223372036854775807), "\x00\x7f\xff\xff\xff\xff\xff\xff\xff"],
            'long -1'  => [new LongType(-1), "\x00\xff\xff\xff\xff\xff\xff\xff\xff"],
            'long -2'  => [new LongType(-2), "\x00\xff\xff\xff\xff\xff\xff\xff\xfe"],
            'long min' => [new LongType(-9223372036854775807), "\x00\x80\x00\x00\x00\x00\x00\x00\x01"],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'int' => [new IntType(null)],
        ];
    }
}

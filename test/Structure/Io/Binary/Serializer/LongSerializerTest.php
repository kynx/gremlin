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
            'long null' => [new LongType(null), "\x01"],
            'long -2'   => [new LongType(-2), "\x00\xff\xff\xff\xff\xff\xff\xff\xfe"],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'int' => [new IntType(null)],
        ];
    }
}

<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\ByteSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Type\ByteType;
use Kynx\Gremlin\Structure\Type\IntType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ByteSerializer::class)]
final class ByteSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): SerializerInterface
    {
        return new ByteSerializer();
    }

    public static function serializableTypesProvider(): array
    {
        return [
            'null' => [new ByteType(null), "\x01"],
            'byte' => [new ByteType(255), "\x00\xff"],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'int' => [new IntType(255)],
        ];
    }
}

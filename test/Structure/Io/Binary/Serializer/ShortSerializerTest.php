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
            'short null' => [new ShortType(null), "\x01"],
            'int -2'     => [new ShortType(-2), "\x00\xff\xfe"],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'int' => [new IntType(255)],
        ];
    }
}

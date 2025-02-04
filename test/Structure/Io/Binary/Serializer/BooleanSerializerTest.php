<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\BooleanSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Type\BooleanType;
use Kynx\Gremlin\Structure\Type\IntType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(BooleanSerializer::class)]
final class BooleanSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): SerializerInterface
    {
        return new BooleanSerializer();
    }

    public static function serializableTypesProvider(): array
    {
        return [
            'null'  => [new BooleanType(null), "\x01"],
            'false' => [new BooleanType(false), "\x00\x00"],
            'true'  => [new BooleanType(true), "\x00\x01"],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'string' => [new IntType(null)],
        ];
    }
}

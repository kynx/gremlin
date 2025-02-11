<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\UnspecifiedNullObjectSerializer;
use Kynx\Gremlin\Structure\Type\IntType;
use Kynx\Gremlin\Structure\Type\UnspecifiedNullObject;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UnspecifiedNullObjectSerializer::class)]
final class UnspecifiedNullObjectSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): SerializerInterface
    {
        return new UnspecifiedNullObjectSerializer();
    }

    public static function serializableTypesProvider(): array
    {
        return [
            'null' => [new UnspecifiedNullObject(), ""],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'int' => [new IntType(null)],
        ];
    }
}

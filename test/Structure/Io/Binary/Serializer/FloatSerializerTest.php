<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\FloatSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Type\DoubleType;
use Kynx\Gremlin\Structure\Type\FloatType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FloatSerializer::class)]
final class FloatSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): SerializerInterface
    {
        return new FloatSerializer();
    }

    public static function serializableTypesProvider(): array
    {
        return [
            'float null'  => [new FloatType(null), "\x01"],
            'float 0.375' => [new FloatType(0.375), "\x00\x3e\xc0\x00\x00"],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'double' => [new DoubleType(null)],
        ];
    }
}

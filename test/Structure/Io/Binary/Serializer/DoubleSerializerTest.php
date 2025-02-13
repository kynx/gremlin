<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\DoubleSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Type\DoubleType;
use Kynx\Gremlin\Structure\Type\IntType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DoubleSerializer::class)]
final class DoubleSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): SerializerInterface
    {
        return new DoubleSerializer();
    }

    public static function serializableTypesProvider(): array
    {
        return [
            'double null'       => [new DoubleType(null), "\x01"],
            'double 0.00390625' => [new DoubleType(0.00390625), "\x00\x3f\x70\x00\x00\x00\x00\x00\x00"],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'int' => [new IntType(null)],
        ];
    }
}

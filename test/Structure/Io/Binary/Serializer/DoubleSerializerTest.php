<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\DoubleSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Type\DoubleType;
use Kynx\Gremlin\Structure\Type\IntType;
use PHPUnit\Framework\Attributes\CoversClass;

use const INF;
use const NAN;

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
            'null'              => [new DoubleType(null), "\x01"],
            'double 0'          => [new DoubleType(0.0), "\x00\x00\x00\x00\x00\x00\x00\x00\x00"],
            'double 1'          => [new DoubleType(1.0), "\x00\x3f\xf0\x00\x00\x00\x00\x00\x00"],
            'double 0.1'        => [new DoubleType(0.1), "\x00\x3f\xb9\x99\x99\x99\x99\x99\x9a"],
            'double 0.375'      => [new DoubleType(0.375), "\x00\x3F\xD8\x00\x00\x00\x00\x00\x00"],
            'double 0.00390625' => [new DoubleType(0.00390625), "\x00\x3f\x70\x00\x00\x00\x00\x00\x00"],
            'infinity'          => [new DoubleType(INF), "\x00\x7F\xF0\x00\x00\x00\x00\x00\x00"],
            'negative infinity' => [new DoubleType(-INF), "\x00\xFF\xF0\x00\x00\x00\x00\x00\x00"],
            'NaN'               => [new DoubleType(NAN), "\x00\x7F\xF8\x00\x00\x00\x00\x00\x00"],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'int' => [new IntType(null)],
        ];
    }
}

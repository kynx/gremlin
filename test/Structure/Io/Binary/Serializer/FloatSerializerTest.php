<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\FloatSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Type\DoubleType;
use Kynx\Gremlin\Structure\Type\FloatType;
use PHPUnit\Framework\Attributes\CoversClass;

use const INF;
use const NAN;

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
            'null'              => [new FloatType(null), "\x01"],
            'float 1'           => [new FloatType(1.0), "\x00\x3f\x80\x00\x00"],
            'float 0.375'       => [new FloatType(0.375), "\x00\x3e\xc0\x00\x00"],
            'infinity'          => [new FloatType(INF), "\x00\x7F\x80\x00\x00"],
            'negative infinity' => [new FloatType(-INF), "\x00\xFF\x80\x00\x00"],
            'NaN'               => [new FloatType(NAN), "\x00\x7F\xC0\x00\x00"],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'double' => [new DoubleType(null)],
        ];
    }
}

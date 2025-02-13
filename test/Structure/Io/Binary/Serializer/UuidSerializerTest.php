<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\UuidSerializer;
use Kynx\Gremlin\Structure\Type\StringType;
use Kynx\Gremlin\Structure\Type\UuidType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UuidSerializer::class)]
final class UuidSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): SerializerInterface
    {
        return new UuidSerializer();
    }

    public static function serializableTypesProvider(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'null' => [new UuidType(null), "\x01"],
            'uuid' => [new UuidType('00112233-4455-6677-8899-aabbccddeeff'), "\x00\x00\x11\x22\x33\x44\x55\x66\x77\x88\x99\xaa\xbb\xcc\xdd\xee\xff"],
        ];
        // phpcs:enable
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'string' => [new StringType(null)],
        ];
    }
}

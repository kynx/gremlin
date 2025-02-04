<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Brick\Math\BigInteger;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\BigIntegerSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Type\BigIntegerType;
use Kynx\Gremlin\Structure\Type\IntType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(BigIntegerSerializer::class)]
final class BigIntegerSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): SerializerInterface
    {
        return new BigIntegerSerializer();
    }

    public static function serializableTypesProvider(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'null'                             => [new BigIntegerType(null), "\x01"],
            'bigint 0'                         => [new BigIntegerType(BigInteger::of('0')), "\x00\x00\x00\x00\x01\x00"],
            'bigint 1'                         => [new BigIntegerType(BigInteger::of('1')), "\x00\x00\x00\x00\x01\x01"],
            'bigint 127'                       => [new BigIntegerType(BigInteger::of('127')), "\x00\x00\x00\x00\x01\x7F"],
            'bigint 128'                       => [new BigIntegerType(BigInteger::of('128')), "\x00\x00\x00\x00\x02\x00\x80"],
            'bigint 160'                       => [new BigIntegerType(BigInteger::of('160')), "\x00\x00\x00\x00\x02\x00\xA0"],
            'bigint 32767'                     => [new BigIntegerType(BigInteger::of('32767')), "\x00\x00\x00\x00\x02\x7F\xFF"],
            'bigint 32768'                     => [new BigIntegerType(BigInteger::of('32768')), "\x00\x00\x00\x00\x03\x00\x80\x00"],
            'bigint 8388607'                   => [new BigIntegerType(BigInteger::of('8388607')), "\x00\x00\x00\x00\x03\x7F\xFF\xFF"],
            'bigint 8388608'                   => [new BigIntegerType(BigInteger::of('8388608')), "\x00\x00\x00\x00\x04\x00\x80\x00\x00"],
            'bigint 2147483647'                => [new BigIntegerType(BigInteger::of('2147483647')), "\x00\x00\x00\x00\x04\x7F\xFF\xFF\xFF"],
            'bigint 2147483648'                => [new BigIntegerType(BigInteger::of('2147483648')), "\x00\x00\x00\x00\x05\x00\x80\x00\x00\x00"],
            'bigint 549755813887'              => [new BigIntegerType(BigInteger::of('549755813887')), "\x00\x00\x00\x00\x05\x7F\xFF\xFF\xFF\xFF"],
            'bigint 549755813888'              => [new BigIntegerType(BigInteger::of('549755813888')), "\x00\x00\x00\x00\x06\x00\x80\x00\x00\x00\x00"],
            'bigint 140737488355327'           => [new BigIntegerType(BigInteger::of('140737488355327')), "\x00\x00\x00\x00\x06\x7F\xFF\xFF\xFF\xFF\xFF"],
            'bigint 140737488355328'           => [new BigIntegerType(BigInteger::of('140737488355328')), "\x00\x00\x00\x00\x07\x00\x80\x00\x00\x00\x00\x00"],
            'bigint 36028797018963967'         => [new BigIntegerType(BigInteger::of('36028797018963967')), "\x00\x00\x00\x00\x07\x7F\xFF\xFF\xFF\xFF\xFF\xFF"],
            'bigint 36028797018963968'         => [new BigIntegerType(BigInteger::of('36028797018963968')), "\x00\x00\x00\x00\x08\x00\x80\x00\x00\x00\x00\x00\x00"],
            'bigint 9223372036854775807'       => [new BigIntegerType(BigInteger::of('9223372036854775807')), "\x00\x00\x00\x00\x08\x7F\xFF\xFF\xFF\xFF\xFF\xFF\xFF"],
            'bigint 9223372036854775808'       => [new BigIntegerType(BigInteger::of('9223372036854775808')), "\x00\x00\x00\x00\x09\x00\x80\x00\x00\x00\x00\x00\x00\x00"],
            'bigint 2361183241434822606847'    => [new BigIntegerType(BigInteger::of('2361183241434822606847')), "\x00\x00\x00\x00\x09\x7F\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF"],
            'bigint 2361183241434822606848'    => [new BigIntegerType(BigInteger::of('2361183241434822606848')), "\x00\x00\x00\x00\x0A\x00\x80\x00\x00\x00\x00\x00\x00\x00\x00"],
            'bigint 604462909807314587353087'  => [new BigIntegerType(BigInteger::of('604462909807314587353087')), "\x00\x00\x00\x00\x0A\x7F\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF"],
            'bigint -1'                        => [new BigIntegerType(BigInteger::of('-1')), "\x00\x00\x00\x00\x01\xFF"],
            'bigint -128'                      => [new BigIntegerType(BigInteger::of('-128')), "\x00\x00\x00\x00\x01\x80"],
            'bigint -129'                      => [new BigIntegerType(BigInteger::of('-129')), "\x00\x00\x00\x00\x02\xFF\x7F"],
            'bigint -32768'                    => [new BigIntegerType(BigInteger::of('-32768')), "\x00\x00\x00\x00\x02\x80\x00"],
            'bigint -32769'                    => [new BigIntegerType(BigInteger::of('-32769')), "\x00\x00\x00\x00\x03\xFF\x7F\xFF"],
            'bigint -8388608'                  => [new BigIntegerType(BigInteger::of('-8388608')), "\x00\x00\x00\x00\x03\x80\x00\x00"],
            'bigint -8388609'                  => [new BigIntegerType(BigInteger::of('-8388609')), "\x00\x00\x00\x00\x04\xFF\x7F\xFF\xFF"],
            'bigint -2147483648'               => [new BigIntegerType(BigInteger::of('-2147483648')), "\x00\x00\x00\x00\x04\x80\x00\x00\x00"],
            'bigint -2147483649'               => [new BigIntegerType(BigInteger::of('-2147483649')), "\x00\x00\x00\x00\x05\xFF\x7F\xFF\xFF\xFF"],
            'bigint -549755813888'             => [new BigIntegerType(BigInteger::of('-549755813888')), "\x00\x00\x00\x00\x05\x80\x00\x00\x00\x00"],
            'bigint -549755813889'             => [new BigIntegerType(BigInteger::of('-549755813889')), "\x00\x00\x00\x00\x06\xFF\x7F\xFF\xFF\xFF\xFF"],
            'bigint -140737488355328'          => [new BigIntegerType(BigInteger::of('-140737488355328')), "\x00\x00\x00\x00\x06\x80\x00\x00\x00\x00\x00"],
            'bigint -140737488355329'          => [new BigIntegerType(BigInteger::of('-140737488355329')), "\x00\x00\x00\x00\x07\xFF\x7F\xFF\xFF\xFF\xFF\xFF"],
            'bigint -36028797018963968'        => [new BigIntegerType(BigInteger::of('-36028797018963968')), "\x00\x00\x00\x00\x07\x80\x00\x00\x00\x00\x00\x00"],
            'bigint -36028797018963969'        => [new BigIntegerType(BigInteger::of('-36028797018963969')), "\x00\x00\x00\x00\x08\xFF\x7F\xFF\xFF\xFF\xFF\xFF\xFF"],
            'bigint -9223372036854775808'      => [new BigIntegerType(BigInteger::of('-9223372036854775808')), "\x00\x00\x00\x00\x08\x80\x00\x00\x00\x00\x00\x00\x00"],
            'bigint -9223372036854775809'      => [new BigIntegerType(BigInteger::of('-9223372036854775809')), "\x00\x00\x00\x00\x09\xFF\x7F\xFF\xFF\xFF\xFF\xFF\xFF\xFF"],
            'bigint -2361183241434822606848'   => [new BigIntegerType(BigInteger::of('-2361183241434822606848')), "\x00\x00\x00\x00\x09\x80\x00\x00\x00\x00\x00\x00\x00\x00"],
            'bigint -2361183241434822606849'   => [new BigIntegerType(BigInteger::of('-2361183241434822606849')), "\x00\x00\x00\x00\x0A\xFF\x7F\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF"],
            'bigint -604462909807314587353088' => [new BigIntegerType(BigInteger::of('-604462909807314587353088')), "\x00\x00\x00\x00\x0A\x80\x00\x00\x00\x00\x00\x00\x00\x00\x00"],
            'bigint -604462909807314587353089' => [new BigIntegerType(BigInteger::of('-604462909807314587353089')), "\x00\x00\x00\x00\x0B\xFF\x7F\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF"],
        ];
        // phpcs:enable
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'int' => [new IntType(null)],
        ];
    }
}

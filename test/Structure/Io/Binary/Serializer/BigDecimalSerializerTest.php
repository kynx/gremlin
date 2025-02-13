<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Brick\Math\BigDecimal;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\BigDecimalSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Type\BigDecimalType;
use Kynx\Gremlin\Structure\Type\BooleanType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(BigDecimalSerializer::class)]
final class BigDecimalSerializerTest extends AbstractSerializerTestCase
{
    protected function getSerializer(): SerializerInterface
    {
        return new BigDecimalSerializer();
    }

    public static function serializableTypesProvider(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'bigdec null'                        => [new BigDecimalType(null), "\x01"],
            'bigdec 1'                           => [new BigDecimalType(BigDecimal::of('1')), "\x00\x00\x00\x00\x00\x00\x00\x00\x01\x01"],
            'bigdec 0.01'                        => [new BigDecimalType(BigDecimal::of('0.01')), "\x00\x00\x00\x00\x02\x00\x00\x00\x01\x01"],
            'bigdec 12.7'                        => [new BigDecimalType(BigDecimal::of('12.7')), "\x00\x00\x00\x00\x01\x00\x00\x00\x01\x7F"],
            'bigdec 127'                         => [new BigDecimalType(BigDecimal::of('127')), "\x00\x00\x00\x00\x00\x00\x00\x00\x01\x7F"],
            'bigdec 604462909807314587353087'    => [new BigDecimalType(BigDecimal::of('604462909807314587353087')), "\x00\x00\x00\x00\x00\x00\x00\x00\x0A\x7F\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF"],
            'bigdec 6044629098.07314587353087'   => [new BigDecimalType(BigDecimal::of('6044629098.07314587353087')), "\x00\x00\x00\x00\x0e\x00\x00\x00\x0A\x7F\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF"],
            'bigdec 0.604462909807314587353087'  => [new BigDecimalType(BigDecimal::of('0.604462909807314587353087')), "\x00\x00\x00\x00\x18\x00\x00\x00\x0A\x7F\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF"],
            'bigdec -1'                          => [new BigDecimalType(BigDecimal::of('-1')), "\x00\x00\x00\x00\x00\x00\x00\x00\x01\xFF"],
            'bigdec -0.01'                       => [new BigDecimalType(BigDecimal::of('-0.01')), "\x00\x00\x00\x00\x02\x00\x00\x00\x01\xFF"],
            'bigdec -12.8'                       => [new BigDecimalType(BigDecimal::of('-12.8')), "\x00\x00\x00\x00\x01\x00\x00\x00\x01\x80"],
            'bigdec -128'                        => [new BigDecimalType(BigDecimal::of('-128')), "\x00\x00\x00\x00\x00\x00\x00\x00\x01\x80"],
            'bigdec -604462909807314587353089'   => [new BigDecimalType(BigDecimal::of('-604462909807314587353089')), "\x00\x00\x00\x00\x00\x00\x00\x00\x0B\xFF\x7F\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF"],
            'bigdec -6044629098.07314587353089'  => [new BigDecimalType(BigDecimal::of('-6044629098.07314587353089')), "\x00\x00\x00\x00\x0e\x00\x00\x00\x0B\xFF\x7F\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF"],
            'bigdec -0.604462909807314587353089' => [new BigDecimalType(BigDecimal::of('-0.604462909807314587353089')), "\x00\x00\x00\x00\x18\x00\x00\x00\x0B\xFF\x7F\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF"],
        ];
        // phpcs:enable
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'boolean' => [new BooleanType(null)],
        ];
    }

    public function testReadNegativeScale(): void
    {
        $expected = new BigDecimalType(BigDecimal::of("100"));
        $stream   = $this->getStream("\x00\xff\xff\xff\xfe\x00\x00\x00\x01\x01");
        $actual   = $this->getSerializer()->unserialize($stream, $this->getReader());
        self::assertEquals($expected, $actual);
    }
}

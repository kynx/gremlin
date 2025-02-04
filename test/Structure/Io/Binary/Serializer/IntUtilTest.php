<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Serializer\IntUtil;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(IntUtil::class)]
final class IntUtilTest extends TestCase
{
    #[DataProvider('uInt16Provider')]
    public function testPackUInt16(int $value, string $expected): void
    {
        $actual = IntUtil::packUInt($value, 16);
        self::assertSame($expected, $actual);
    }

    #[DataProvider('uInt16Provider')]
    public function testUnpackUInt16(int $expected, string $value): void
    {
        $actual = IntUtil::unpackUInt($value, 16);
        self::assertSame($expected, $actual);
    }

    public static function uInt16Provider(): array
    {
        return [
            'uint 0'   => [0, "\x00\x00"],
            'uint 1'   => [1, "\x00\x01"],
            'uint 256' => [256, "\x01\x00"],
            'uint max' => [65535, "\xff\xff"],
        ];
    }

    #[DataProvider('uInt32Provider')]
    public function testPackUInt32(int $value, string $expected): void
    {
        $actual = IntUtil::packUInt($value);
        self::assertSame($expected, $actual);
    }

    #[DataProvider('uInt32Provider')]
    public function testUnpackUInt32(int $expected, string $value): void
    {
        $actual = IntUtil::unpackUInt($value);
        self::assertSame($expected, $actual);
    }

    public static function uInt32Provider(): array
    {
        return [
            'uint 0'   => [0, "\x00\x00\x00\x00"],
            'uint 1'   => [1, "\x00\x00\x00\x01"],
            'uint 256' => [256, "\x00\x00\x01\x00"],
            'uint max' => [4294967295, "\xff\xff\xff\xff"],
        ];
    }

    public function testPackUIntWithInvalidLengthThrowsException(): void
    {
        self::expectException(WriterException::class);
        self::expectExceptionMessage("Invalid integer length: 24");
        IntUtil::packUInt(0, 24);
    }

    public function testUnpackUIntWithInvalidLengthThrowsException(): void
    {
        self::expectException(WriterException::class);
        self::expectExceptionMessage("Invalid integer length: 24");
        IntUtil::unpackUInt("\x00\x00\x00");
    }

    #[DataProvider('int16Provider')]
    public function testPackInt16(int $value, string $expected): void
    {
        $actual = IntUtil::packInt($value, 16);
        self::assertSame($expected, $actual);
    }

    #[DataProvider('int16Provider')]
    public function testUnpackInt16(int $expected, string $value): void
    {
        $actual = IntUtil::unpackInt($value);
        self::assertSame($expected, $actual);
    }

    public static function int16Provider(): array
    {
        return [
            'int 0'   => [0, "\x00\x00"],
            'int 1'   => [1, "\x00\x01"],
            'int 256' => [256, "\x01\x00"],
            'int max' => [32767, "\x7f\xff"],
            'int -1'  => [-1, "\xff\xff"],
            'int -2'  => [-2, "\xff\xfe"],
            'int min' => [-32767, "\x80\x01"],
        ];
    }

    #[DataProvider('int32Provider')]
    public function testPackInt32(int $value, string $expected): void
    {
        $actual = IntUtil::packInt($value);
        self::assertSame($expected, $actual);
    }

    #[DataProvider('int32Provider')]
    public function testUnpackInt32(int $expected, string $value): void
    {
        $actual = IntUtil::unpackInt($value);
        self::assertSame($expected, $actual);
    }

    public static function int32Provider(): array
    {
        return [
            'int 0'   => [0, "\x00\x00\x00\x00"],
            'int 1'   => [1, "\x00\x00\x00\x01"],
            'int 256' => [256, "\x00\x00\x01\x00"],
            'int max' => [2147483647, "\x7f\xff\xff\xff"],
            'int -1'  => [-1, "\xff\xff\xff\xff"],
            'int -2'  => [-2, "\xff\xff\xff\xfe"],
            'int min' => [-2147483647, "\x80\x00\x00\x01"],
        ];
    }
}

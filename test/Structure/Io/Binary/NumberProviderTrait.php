<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary;

use const INF;
use const NAN;

trait NumberProviderTrait
{
    public static function byteProvider(): array
    {
        return [
            'byte 0'   => [0, "\x00"],
            "byte 1"   => [1, "\x01"],
            "byte 255" => [255, "\xff"],
        ];
    }

    public static function shortProvider(): array
    {
        return [
            'short 0'   => [0, "\x00\x00"],
            'short 1'   => [1, "\x00\x01"],
            'short 256' => [256, "\x01\x00"],
            'short max' => [32767, "\x7f\xff"],
            'short -1'  => [-1, "\xff\xff"],
            'short -2'  => [-2, "\xff\xfe"],
            'short min' => [-32767, "\x80\x01"],
        ];
    }

    public static function uShortProvider(): array
    {
        return [
            'ushort 0'   => [0, "\x00\x00"],
            'ushort 1'   => [1, "\x00\x01"],
            'ushort 256' => [256, "\x01\x00"],
            'ushort max' => [65535, "\xff\xff"],
        ];
    }

    public static function intProvider(): array
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

    public static function uIntProvider(): array
    {
        return [
            'uint 0'   => [0, "\x00\x00\x00\x00"],
            'uint 1'   => [1, "\x00\x00\x00\x01"],
            'uint 256' => [256, "\x00\x00\x01\x00"],
            'uint max' => [4294967295, "\xff\xff\xff\xff"],
        ];
    }

    public static function longProvider(): array
    {
        return [
            'long 0'   => [0, "\x00\x00\x00\x00\x00\x00\x00\x00"],
            'long 1'   => [1, "\x00\x00\x00\x00\x00\x00\x00\x01"],
            'long max' => [9223372036854775807, "\x7f\xff\xff\xff\xff\xff\xff\xff"],
            'long -1'  => [-1, "\xff\xff\xff\xff\xff\xff\xff\xff"],
            'long -2'  => [-2, "\xff\xff\xff\xff\xff\xff\xff\xfe"],
            'long min' => [-9223372036854775807, "\x80\x00\x00\x00\x00\x00\x00\x01"],
        ];
    }

    public static function floatProvider(): array
    {
        return [
            'float 1'                 => [1.0, "\x3f\x80\x00\x00"],
            'float 0.375'             => [0.375, "\x3e\xc0\x00\x00"],
            'float infinity'          => [INF, "\x7F\x80\x00\x00"],
            'float negative infinity' => [-INF, "\xFF\x80\x00\x00"],
            'float NaN'               => [NAN, "\x7F\xC0\x00\x00"],
        ];
    }

    public static function doubleProvider(): array
    {
        return [
            'double 0'                 => [0.0, "\x00\x00\x00\x00\x00\x00\x00\x00"],
            'double 1'                 => [1.0, "\x3f\xf0\x00\x00\x00\x00\x00\x00"],
            'double 0.1'               => [0.1, "\x3f\xb9\x99\x99\x99\x99\x99\x9a"],
            'double 0.375'             => [0.375, "\x3F\xD8\x00\x00\x00\x00\x00\x00"],
            'double 0.00390625'        => [0.00390625, "\x3f\x70\x00\x00\x00\x00\x00\x00"],
            'double infinity'          => [INF, "\x7F\xF0\x00\x00\x00\x00\x00\x00"],
            'double negative infinity' => [-INF, "\xFF\xF0\x00\x00\x00\x00\x00\x00"],
            'double NaN'               => [NAN, "\x7F\xF8\x00\x00\x00\x00\x00\x00"],
        ];
    }
}

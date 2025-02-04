<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\WriterException;

use function assert;
use function bin2hex;
use function dechex;
use function hex2bin;
use function hexdec;
use function is_array;
use function ord;
use function pack;
use function str_pad;
use function strlen;
use function substr;
use function unpack;

use const STR_PAD_LEFT;

final readonly class IntUtil
{
    public static function unpackUInt(string $binary): int
    {
        $unpacked = unpack(self::format(strlen($binary) * 8), $binary);
        assert(is_array($unpacked) && isset($unpacked[1]));
        return (int) $unpacked[1];
    }

    public static function packUInt(int $int, int $length = 32): string
    {
        return substr(pack(self::format($length), $int), 0 - $length);
    }

    public static function unpackInt(string $binary): int
    {
        if ($twosCompliment = ord($binary[0]) >= 0x80) {
            $binary = ~$binary;
        }

        return $twosCompliment ? 0 - ((int) hexdec(bin2hex($binary)) + 1) : (int) hexdec(bin2hex($binary));
    }

    public static function packInt(int $int, int $length = 32): string
    {
        $size = (int) ($length / 4);

        return (string) hex2bin(substr(
            str_pad(dechex($int), $size, $int < 0 ? 'f' : '0', STR_PAD_LEFT),
            0 - $size
        ));
    }

    private static function format(int $length): string
    {
        return match ($length) {
            8       => 'C',
            16      => 'n',
            32      => 'N',
            64      => 'J',
            default => throw WriterException::invalidPackLength($length),
        };
    }
}

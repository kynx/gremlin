<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary;

use Kynx\Gremlin\Structure\Io\Binary\BinaryType;
use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BinaryType::class)]
final class BinaryTypeTest extends TestCase
{
    public function testFromChrReturnsType(): void
    {
        $expected = BinaryType::Vertex;
        $actual   = BinaryType::fromChr("\x11");
        self::assertSame($expected, $actual);
    }

    public function testFromUnknownInThrowsException(): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage("Unknown binary type 0xff");
        BinaryType::fromChr("\xff");
    }

    public function testToChrReturnsByte(): void
    {
        $expected = "\x11";
        $actual   = BinaryType::Vertex->toChr();
        self::assertSame($expected, $actual);
    }
}

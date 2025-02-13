<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Exception;

use Kynx\Gremlin\Structure\Io\Binary\Exception\UnderflowException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnderflowException::class)]
final class UnderflowExceptionTest extends TestCase
{
    public function testEmptyStreamDoesNotSetLengths(): void
    {
        $exception = UnderflowException::emptyStream();
        self::assertSame('No more data in stream', $exception->getMessage());
        self::assertNull($exception->getExpectedLength());
        self::assertNull($exception->getActualLength());
    }

    public function testDataNotReadSetsLengths(): void
    {
        $expected  = 1024;
        $read      = 128;
        $exception = UnderflowException::dataNotRead($expected, $read);
        self::assertSame('Expected to read 1024 bytes, received 128', $exception->getMessage());
        self::assertSame($expected, $exception->getExpectedLength());
        self::assertSame($read, $exception->getActualLength());
    }
}

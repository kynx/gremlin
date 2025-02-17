<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Driver\Amp;

use Amp\ByteStream\ReadableBuffer;
use Amp\Websocket\WebsocketMessage;
use Kynx\Gremlin\Driver\Amp\BufferStream;
use Kynx\Gremlin\Driver\Amp\Exception\StreamException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BufferStream::class)]
final class BufferStreamTest extends TestCase
{
    public function testCloseEmptiesBuffer(): void
    {
        $stream = $this->getStream('abc');
        $stream->close();
        self::assertSame('', $stream->getContents());
    }

    public function testDetachClosesStreamAndReturnsNull(): void
    {
        $stream = $this->getStream('abc');
        $actual = $stream->detach();
        self::assertNull($actual);
        self::assertSame('', $stream->getContents());
    }

    public function testGetSizeReturnsNull(): void
    {
        $stream = $this->getStream('abc');
        $actual = $stream->getSize();
        self::assertNull($actual);
    }

    public function testTellThrowsException(): void
    {
        $stream = $this->getStream('abc');
        self::expectException(StreamException::class);
        self::expectExceptionMessage("Cannot tell()");
        $stream->tell();
    }

    public function testEof(): void
    {
        $stream = $this->getStream('abc');
        $actual = $stream->eof();
        self::assertFalse($actual);
        $stream->read(3);
        $actual = $stream->eof();
        self::assertTrue($actual);
    }

    public function testIsSeekableReturnsFalse(): void
    {
        $stream = $this->getStream('abc');
        $actual = $stream->isSeekable();
        self::assertFalse($actual);
    }

    public function testSeekThrowsException(): void
    {
        $stream = $this->getStream('abc');
        self::expectException(StreamException::class);
        self::expectExceptionMessage("Cannot seek()");
        $stream->seek(1);
    }

    public function testRewindThrowsException(): void
    {
        $stream = $this->getStream('abc');
        self::expectException(StreamException::class);
        self::expectExceptionMessage("Cannot rewind()");
        $stream->rewind();
    }

    public function testIsWritableReturnsFalse(): void
    {
        $stream = $this->getStream('abc');
        $actual = $stream->isWritable();
        self::assertFalse($actual);
    }

    public function testWriteThrowsException(): void
    {
        $stream = $this->getStream('abc');
        self::expectException(StreamException::class);
        self::expectExceptionMessage("Cannot write()");
        $stream->write('a');
    }

    public function testIsReadableReturnsTrue(): void
    {
        $stream = $this->getStream('abc');
        $actual = $stream->isReadable();
        self::assertTrue($actual);
    }

    public function testReadGreaterLengthEmptiesBuffer(): void
    {
        $expected = 'abc';
        $stream   = $this->getStream($expected);
        $actual   = $stream->read(4);
        self::assertSame($expected, $actual);
        self::assertSame('', $stream->getContents());
    }

    public function testReadRemovesFromBuffer(): void
    {
        $expected = 'bc';
        $stream   = $this->getStream('abc');
        $read     = $stream->read(1);
        self::assertSame('a', $read);
        $actual = $stream->getContents();
        self::assertSame($expected, $actual);
    }

    public function testGetContentsEmptiesBuffer(): void
    {
        $expected = 'abc';
        $stream   = $this->getStream($expected);
        $actual   = $stream->getContents();
        self::assertSame($expected, $actual);
        self::assertSame('', $stream->getContents());
    }

    public function testGetMetadataReturnsEmptyArray(): void
    {
        $stream = $this->getStream('abc');
        $actual = $stream->getMetadata();
        self::assertSame([], $actual);
    }

    public function testGetMetadataReturnsNull(): void
    {
        $stream = $this->getStream('abc');
        $actual = $stream->getMetadata('foo');
        self::assertNull($actual);
    }

    public function testToStringReturnsContentsAndEmptiesBuffer(): void
    {
        $expected = 'abc';
        $stream   = $this->getStream($expected);
        $actual   = (string) $stream;
        self::assertSame($expected, $actual);
        self::assertSame('', $stream->getContents());
    }

    private function getStream(string $contents): BufferStream
    {
        return new BufferStream(WebsocketMessage::fromBinary(new ReadableBuffer($contents)));
    }
}

<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary;

use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Exception\StreamException;
use Kynx\Gremlin\Structure\Io\Binary\Exception\UnderflowException;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\IntSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\IntType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Writer::class)]
final class WriterTest extends TestCase
{
    use NumberProviderTrait;
    use StreamTrait;

    public function testWriteAppendsType(): void
    {
        $expected = "\x01\x00\x00\x00\x00\x01";
        $type     = new IntType(1);
        $stream   = $this->getWrittenStream();
        $writer   = $this->getWriter(new IntSerializer());

        $writer->write($stream, $type);
        self::assertWrittenStreamSame($expected, $stream);
    }

    public function testWriteUnknownTypeThrowsException(): void
    {
        $type   = new IntType(1);
        $stream = $this->getStream();
        $writer = $this->getWriter();

        self::expectException(DomainException::class);
        self::expectExceptionMessage("No serializer found for type '" . $type::class . "'");
        $writer->write($stream, $type);
    }

    public function testWriteNullAppendsFlag(): void
    {
        $stream = $this->getWrittenStream();
        $writer = $this->getWriter();
        $writer->writeNull($stream);
        self::assertWrittenStreamSame("\x01", $stream);
    }

    public function testWriteNotNullAppendsFlag(): void
    {
        $stream = $this->getWrittenStream();
        $writer = $this->getWriter();
        $writer->writeNotNull($stream);
        self::assertWrittenStreamSame("\x00", $stream);
    }

    #[DataProvider('byteProvider')]
    public function testWriteByte(int $byte, string $expected): void
    {
        $stream = $this->getWrittenStream();
        $writer = $this->getWriter();
        $writer->writeByte($stream, $byte);
        self::assertWrittenStreamSame($expected, $stream);
    }

    #[DataProvider('shortProvider')]
    public function testWriteShort(int $short, string $expected): void
    {
        $stream = $this->getWrittenStream();
        $writer = $this->getWriter();
        $writer->writeShort($stream, $short);
        self::assertWrittenStreamSame($expected, $stream);
    }

    #[DataProvider('intProvider')]
    public function testWriteInt(int $int, string $expected): void
    {
        $stream = $this->getWrittenStream();
        $writer = $this->getWriter();
        $writer->writeInt($stream, $int);
        self::assertWrittenStreamSame($expected, $stream);
    }

    #[DataProvider('uIntProvider')]
    public function testWriteUInt(int $uInt, string $expected): void
    {
        $stream = $this->getWrittenStream();
        $writer = $this->getWriter();
        $writer->writeUInt($stream, $uInt);
        self::assertWrittenStreamSame($expected, $stream);
    }

    #[DataProvider('longProvider')]
    public function testWriteLong(int $long, string $expected): void
    {
        $stream = $this->getWrittenStream();
        $writer = $this->getWriter();
        $writer->writeLong($stream, $long);
        self::assertWrittenStreamSame($expected, $stream);
    }

    #[DataProvider('floatProvider')]
    public function testWriteFloat(float $float, string $expected): void
    {
        $stream = $this->getWrittenStream();
        $writer = $this->getWriter();
        $writer->writeFloat($stream, $float);
        self::assertWrittenStreamSame($expected, $stream);
    }

    #[DataProvider('doubleProvider')]
    public function testWriteDouble(float $double, string $expected): void
    {
        $stream = $this->getWrittenStream();
        $writer = $this->getWriter();
        $writer->writeDouble($stream, $double);
        self::assertWrittenStreamSame($expected, $stream);
    }

    public function testWriteBytesOnClosedStreamThrowsException(): void
    {
        $stream = $this->getStream();
        $stream->close();
        $writer = $this->getWriter();

        self::expectException(StreamException::class);
        self::expectExceptionMessage('Error writing stream');
        $writer->writeBytes($stream, "\x01", 1);
    }

    public function testWriteBytesMismatchedLengthThrowsException(): void
    {
        $stream = $this->getStream();
        $writer = $this->getWriter();

        self::expectException(UnderflowException::class);
        self::expectExceptionMessage("Expected to write 2 bytes, sent 1");
        $writer->writeBytes($stream, "\x01", 2);
    }

    private function getWriter(SerializerInterface ...$serializers): Writer
    {
        return new Writer(...$serializers);
    }
}

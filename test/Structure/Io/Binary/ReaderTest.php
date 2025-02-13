<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary;

use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Exception\StreamException;
use Kynx\Gremlin\Structure\Io\Binary\Exception\UnderflowException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\IntSerializer;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Type\IntType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function is_nan;

#[CoversClass(Reader::class)]
final class ReaderTest extends TestCase
{
    use NumberProviderTrait;
    use StreamTrait;

    public function testReadReturnsType(): void
    {
        $expected   = new IntType(1);
        $serializer = new IntSerializer();
        $reader     = $this->getReader($serializer);
        $stream     = $this->getStream("\x01\x00\x00\x00\x00\x01");

        $actual = $reader->read($stream);
        self::assertEquals($expected, $actual);
    }

    public function testReadUnsupportedTypeThrowsException(): void
    {
        $reader = $this->getReader();
        $stream = $this->getStream("\x01");

        self::expectException(DomainException::class);
        self::expectExceptionMessage("No serializer found for type 0x01");
        $reader->read($stream);
    }

    #[DataProvider('isNullProvider')]
    public function testIsNull(string $flag, bool $expected): void
    {
        $stream = $this->getStream($flag, self::CRYING);
        $reader = $this->getReader();
        $actual = $reader->isNull($stream);
        self::assertSame($expected, $actual);
        self::assertHasRemainingStream($stream);
    }

    public static function isNullProvider(): array
    {
        return [
            "not null" => ["\x00", false],
            "null"     => ["\x01", true],
        ];
    }

    #[DataProvider('byteProvider')]
    public function testReadByte(int $expected, string $byte): void
    {
        $stream = $this->getStream($byte, self::CRYING);
        $reader = $this->getReader();
        $actual = $reader->readByte($stream);
        self::assertSame($expected, $actual);
        self::assertHasRemainingStream($stream);
    }

    #[DataProvider('shortProvider')]
    public function testReadShort(int $expected, string $bytes): void
    {
        $stream = $this->getStream($bytes, self::CRYING);
        $reader = $this->getReader();
        $actual = $reader->readShort($stream);
        self::assertSame($expected, $actual);
        self::assertHasRemainingStream($stream);
    }

    #[DataProvider('intProvider')]
    public function testReadInt(int $expected, string $bytes): void
    {
        $stream = $this->getStream($bytes, self::CRYING);
        $reader = $this->getReader();
        $actual = $reader->readInt($stream);
        self::assertSame($expected, $actual);
        self::assertHasRemainingStream($stream);
    }

    #[DataProvider('uIntProvider')]
    public function testReadUInt(int $expected, string $bytes): void
    {
        $stream = $this->getStream($bytes, self::CRYING);
        $reader = $this->getReader();
        $actual = $reader->readUInt($stream);
        self::assertSame($expected, $actual);
        self::assertHasRemainingStream($stream);
    }

    #[DataProvider('longProvider')]
    public function testReadLong(int $expected, string $bytes): void
    {
        $stream = $this->getStream($bytes, self::CRYING);
        $reader = $this->getReader();
        $actual = $reader->readLong($stream);
        self::assertSame($expected, $actual);
        self::assertHasRemainingStream($stream);
    }

    #[DataProvider('floatProvider')]
    public function testReadFloat(float $expected, string $bytes): void
    {
        $stream = $this->getStream($bytes, self::CRYING);
        $reader = $this->getReader();
        $actual = $reader->readFloat($stream);
        if (is_nan($expected)) {
            self::assertNan($expected);
        } else {
            self::assertSame($expected, $actual);
        }
        self::assertHasRemainingStream($stream);
    }

    #[DataProvider('doubleProvider')]
    public function testReadDouble(float $expected, string $bytes): void
    {
        $stream = $this->getStream($bytes, self::CRYING);
        $reader = $this->getReader();
        $actual = $reader->readDouble($stream);
        if (is_nan($expected)) {
            self::assertNan($expected);
        } else {
            self::assertSame($expected, $actual);
        }
        self::assertHasRemainingStream($stream);
    }

    public function testReadZeroBytesReturnsEmptyString(): void
    {
        $stream = $this->getStream(self::CRYING);
        $reader = $this->getReader();
        $actual = $reader->readBytes($stream, 0);
        self::assertSame('', $actual);
        self::assertHasRemainingStream($stream);
    }

    public function testReadBytesOnClosedStreamThrowsException(): void
    {
        $stream = $this->getStream();
        $stream->close();
        $reader = $this->getReader();

        self::expectException(StreamException::class);
        self::expectExceptionMessage('Error reading stream');
        $reader->readBytes($stream, 1);
    }

    public function testReadBytesOnEmptyStreamThrowsException(): void
    {
        $stream = $this->getStream();
        $reader = $this->getReader();

        self::expectException(UnderflowException::class);
        self::expectExceptionMessage('No more data in stream');
        $reader->readBytes($stream, 1);
    }

    public function testReadBytesMismatchedLengthThrowsException(): void
    {
        $stream = $this->getStream("\xff");
        $reader = $this->getReader();

        self::expectException(UnderflowException::class);
        self::expectExceptionMessage('Expected to read 2 bytes, received 1');
        $reader->readBytes($stream, 2);
    }

    private function getReader(SerializerInterface ...$serializers): Reader
    {
        return new Reader(...$serializers);
    }
}

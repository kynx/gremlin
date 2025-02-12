<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\ReaderException;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\ByteBufferSerializer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\ByteBufferType;
use Kynx\Gremlin\Structure\Type\ByteType;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

use function fclose;
use function fopen;
use function fwrite;
use function is_resource;
use function rewind;
use function str_repeat;

#[CoversClass(ByteBufferSerializer::class)]
final class ByteBufferSerializerTest extends AbstractSerializerTestCase
{
    /** @var resource|closed-resource|null */
    protected $resource;

    protected function tearDown(): void
    {
        parent::tearDown();

        if (is_resource($this->resource)) {
            fclose($this->resource);
            $this->resource = null;
        }
    }

    protected function getSerializer(): ByteBufferSerializer
    {
        return new ByteBufferSerializer();
    }

    #[DataProvider('serializableTypesProvider')]
    public function testReadReturnsValue(TypeInterface $expected, string $bytes): void
    {
        $stream = $this->getStream();
        $stream->write($bytes);
        $stream->rewind();

        $actual = $this->getSerializer()->read($stream, $this->getReader());

        self::assertSame($expected->getValue(), $actual->getValue());
    }

    public static function serializableTypesProvider(): array
    {
        $twoFiveSix = str_repeat("\xff", 256);
        return [
            'zero bytes' => [ByteBufferType::ofString(''), "\x00\x00\x00\x00\x00"],
            'one byte'   => [ByteBufferType::ofString("\xff"), "\x00\x00\x00\x00\x01\xff"],
            '256 bytes'  => [ByteBufferType::ofString($twoFiveSix), "\x00\x00\x00\x01\x00" . $twoFiveSix],
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            'byte' => [new ByteType(null)],
        ];
    }

    public function testReadNullBufferReturnsEmptyBuffer(): void
    {
        $stream     = $this->getStream("\x01");
        $serializer = $this->getSerializer();

        $actual = $serializer->read($stream, $this->getReader());
        self::assertSame(0, $actual->getLength());
        self::assertSame("", $actual->getValue());
    }

    public function testReadEofStreamThrowsException(): void
    {
        $stream     = $this->getStream(
            "\x00",
            "\x00\x00\x00\x01",
        );
        $serializer = $this->getSerializer();

        self::expectException(ReaderException::class);
        self::expectExceptionMessage("Expected to read 1 bytes from stream, got 0");
        $serializer->read($stream, $this->getReader());
    }

    public function testReadInvalidLengthThrowsException(): void
    {
        $stream     = $this->getStream(
            "\x00",
            "\x00\x00\x20\x01", // 8193
            str_repeat("\x00", 8192)
        );
        $serializer = $this->getSerializer();

        self::expectException(ReaderException::class);
        self::expectExceptionMessage("Expected to read 8193 bytes from stream, got 8192");
        $serializer->read($stream, $this->getReader());
    }

    public function testWriteDoesNotWriteBeyondLength(): void
    {
        $expected   = "\x00\x00\x00\x00\x01\xff";
        $resource   = $this->getResource("\xff" . self::CRYING);
        $buffer     = ByteBufferType::ofResource($resource, 1);
        $stream     = $this->getStream();
        $serializer = $this->getSerializer();

        $serializer->write($stream, $buffer, $this->getWriter());
        $stream->rewind();
        $actual = $stream->getContents();
        self::assertSame($expected, $actual);
    }

    public function testWriteEofBufferThrowsException(): void
    {
        $resource = $this->getResource("\xff");
        $buffer   = ByteBufferType::ofResource($resource, 1);
        fclose($resource);
        $serializer = $this->getSerializer();

        self::expectException(WriterException::class);
        self::expectExceptionMessage("Expected to write 1 bytes, only wrote 0");
        $serializer->write($this->getStream(), $buffer, $this->getWriter());
    }

    public function testWriteInvalidLengthThrowsException(): void
    {
        $resource   = $this->getResource(str_repeat("\xff", 8192));
        $buffer     = ByteBufferType::ofResource($resource, 8193);
        $serializer = $this->getSerializer();

        self::expectException(WriterException::class);
        self::expectExceptionMessage("Expected to write 8193 bytes, only wrote 8192");
        $serializer->write($this->getStream(), $buffer, $this->getWriter());
    }

    /**
     * @return resource
     */
    private function getResource(string $contents)
    {
        $resource = fopen('php://memory', 'w+');
        self::assertIsResource($resource);
        $this->resource = $resource;
        fwrite($this->resource, $contents);
        rewind($this->resource);

        return $this->resource;
    }
}

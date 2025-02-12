<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Type;

use GuzzleHttp\Psr7\FnStream;
use GuzzleHttp\Psr7\StreamWrapper;
use Kynx\Gremlin\Structure\Type\ByteBufferType;
use Kynx\Gremlin\Structure\Type\TypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function fclose;
use function fopen;
use function fread;
use function fwrite;
use function iterator_to_array;
use function rewind;
use function str_repeat;
use function substr;

#[CoversClass(ByteBufferType::class)]
final class ByteBufferTypeTest extends TestCase
{
    public function testOfResourceThrowsExceptionWhenArgumentIsNotAResource(): void
    {
        self::expectException(TypeException::class);
        self::expectExceptionMessage("Expected value of type 'resource', got: string");
        /** @psalm-suppress InvalidArgument That's what we're testing... */
        ByteBufferType::ofResource('foo');
    }

    public function testOfResourceThrowsExceptionWhenLengthNotAvailable(): void
    {
        $stream   = new FnStream([
            'isReadable' => static fn(): true => true,
            'isWritable' => static fn(): false => false,
            'getSize'    => static fn(): null => null, // so fstat() will return false
        ]);
        $resource = StreamWrapper::getResource($stream);

        self::expectException(TypeException::class);
        self::expectExceptionMessage('Cannot calculate buffer length');
        ByteBufferType::ofResource($resource);
    }

    public function testOfResourceReturnsBufferedResource(): void
    {
        $expected = 'foo bar';
        $resource = $this->getResource();
        fwrite($resource, $expected);
        rewind($resource);

        $type   = ByteBufferType::ofResource($resource);
        $actual = $type->getValue();
        self::assertSame($expected, $actual);
    }

    public function testOfStringReturnsBufferedString(): void
    {
        $expected = 'foo bar';

        $type   = ByteBufferType::ofString($expected);
        $actual = $type->getValue();
        self::assertSame($expected, $actual);
    }

    public function testOfByteArrayThrowsExceptionOnNonInt(): void
    {
        $bytes = [0x00, 0x0d, 'b'];
        self::expectException(TypeException::class);
        self::expectExceptionMessage("Expected value of type 'integer', got: string");
        /** @psalm-suppress InvalidArgument That's what we're testing... */
        ByteBufferType::ofByteArray($bytes);
    }

    #[DataProvider('outOfBoundsProvider')]
    public function testOfByteArrayOutOfBoundsThrowsException(int $byte): void
    {
        self::expectException(TypeException::class);
        self::expectExceptionMessage("Expected value between 0 and 255, got $byte");
        /** @psalm-suppress ArgumentTypeCoercion That's what we're testing... */
        ByteBufferType::ofByteArray([$byte]);
    }

    public static function outOfBoundsProvider(): array
    {
        return [
            'int -1'  => [-1],
            'int 256' => [256],
        ];
    }

    public function testOfByteArrayBuffersBytes(): void
    {
        $bytes    = [0x00, 0x0d, 0x2a];
        $expected = "\x00\x0d\x2a";

        $type   = ByteBufferType::ofByteArray($bytes);
        $actual = $type->getValue();
        self::assertSame($expected, $actual);
    }

    public function testGetValueReturnsEmptyForClosedResource(): void
    {
        $resource = $this->getResource();

        $type = ByteBufferType::ofResource($resource);
        fclose($resource);
        $actual = $type->getValue();
        self::assertSame('', $actual);
    }

    public function testGetValueClosesResource(): void
    {
        $resource = $this->getResource();

        $type = ByteBufferType::ofResource($resource);
        $type->getValue();

        self::assertIsClosedResource($resource);
    }

    public function testGetLengthReturnsLength(): void
    {
        $expected = 42;
        $type     = ByteBufferType::ofString(str_repeat((string) 0x00, 42));
        $actual   = $type->getLength();
        self::assertSame($expected, $actual);
    }

    public function testReadClosedResourceReturnsEmptyString(): void
    {
        $resource = $this->getResource();
        fwrite($resource, 'a');
        $type = ByteBufferType::ofResource($resource);
        fclose($resource);

        $actual = $type->read(1);
        self::assertSame('', $actual);
    }

    public function testReadReturnsPartialContents(): void
    {
        $contents = str_repeat((string) 0xff, 42);
        $expected = substr($contents, 0, 21);
        $resource = $this->getResource();
        fwrite($resource, $contents);
        rewind($resource);

        $type   = ByteBufferType::ofResource($resource);
        $actual = $type->read(21);
        self::assertSame($expected, $actual);
        self::assertIsResource($resource);
    }

    public function testReadReturnsRemainingContentsAndClosesResource(): void
    {
        $expected = str_repeat((string) 0x00, 8);
        $resource = $this->getResource();
        fwrite($resource, $expected);
        rewind($resource);

        $type   = ByteBufferType::ofResource($resource);
        $actual = $type->read(16);
        self::assertSame($expected, $actual);
        self::assertIsClosedResource($resource);
    }

    public function testEofReturnsFalseForReadableResource(): void
    {
        $type   = ByteBufferType::ofString('foo foo');
        $actual = $type->eof();
        self::assertFalse($actual);
    }

    public function testEofReturnsTrueForClosedResource(): void
    {
        $resource = $this->getResource();
        fwrite($resource, 'foo');
        rewind($resource);
        $type = ByteBufferType::ofResource($resource);
        fclose($resource);

        $actual = $type->eof();
        self::assertTrue($actual);
    }

    public function testEofReturnsTrueForReadResource(): void
    {
        $resource = $this->getResource();
        fwrite($resource, 'foo');
        rewind($resource);
        fread($resource, 4);

        $type   = ByteBufferType::ofResource($resource);
        $actual = $type->eof();
        self::assertTrue($actual);
    }

    public function testGetIteratorYieldsNothingForClosedResource(): void
    {
        $resource = $this->getResource();
        $type     = ByteBufferType::ofResource($resource);
        fclose($resource);

        $actual = iterator_to_array($type->getIterator());
        self::assertSame([], $actual);
    }

    public function testGetIteratorReturnsBytes(): void
    {
        $expected = [0x00, 0x0d, 0x2a];
        $type     = ByteBufferType::ofByteArray($expected);

        $actual = iterator_to_array($type->getIterator());
        self::assertSame($expected, $actual);
    }

    public function testGetIteratorClosesResource(): void
    {
        $resource = $this->getResource();
        $type     = ByteBufferType::ofResource($resource);

        $actual = iterator_to_array($type->getIterator());
        self::assertSame([], $actual);
        self::assertIsClosedResource($resource);
    }

    public function testGetByteArrayReturnsBytes(): void
    {
        $expected = [0x00, 0x0d, 0x2a];
        $type     = ByteBufferType::ofByteArray($expected);

        $actual = $type->getByteArray();
        self::assertSame($expected, $actual);
    }

    public function testIsEqualReturnsFalse(): void
    {
        $type   = ByteBufferType::ofString('foo');
        $actual = $type->equals($type);
        self::assertFalse($actual);
    }

    public function testToStringReturnsLength(): void
    {
        $expected = '[3 byte buffer]';
        $type     = ByteBufferType::ofString('foo');
        $actual   = (string) $type;
        self::assertSame($expected, $actual);
    }

    public function testDestructorClosesResource(): void
    {
        $resource = $this->getResource();
        $type     = ByteBufferType::ofResource($resource);
        unset($type);
        self::assertIsClosedResource($resource);
    }

    /**
     * @return resource
     */
    private function getResource()
    {
        $resource = fopen('php://memory', 'w+');
        self::assertIsResource($resource);

        return $resource;
    }
}

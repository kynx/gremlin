<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use GuzzleHttp\Psr7\Stream;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Io\Binary\WriterException;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

use function fopen;
use function is_float;
use function is_nan;

abstract class AbstractSerializerTestCase extends TestCase
{
    abstract protected function getSerializer(): SerializerInterface;

    /**
     * @return array<string, list{TypeInterface, string}>
     */
    abstract public static function serializableTypesProvider(): array;

    /**
     * @return array<string, list{TypeInterface}>
     */
    abstract public static function invalidTypesProvider(): array;

    #[DataProvider('serializableTypesProvider')]
    public function testWriteAppendsToStream(TypeInterface $type, string $expected): void
    {
        $stream = $this->getStream();
        $stream->write("\xff");

        $this->getSerializer()->write($stream, $type, $this->getWriter());
        $stream->rewind();
        self::assertSame("\xff", $stream->read(1));
        $actual = $stream->read(1024);
        self::assertSame($expected, $actual);
    }

    #[DataProvider('serializableTypesProvider')]
    public function testReadReturnsValue(TypeInterface $expected, string $bytes): void
    {
        $stream = $this->getStream();
        $stream->write($bytes);
        $stream->rewind();

        $actual = $this->getSerializer()->read($stream, $this->getReader());

        $value = $expected->getValue();
        if ($value === null) {
            self::assertInstanceOf($expected::class, $actual);
            self::assertNull($actual->getValue());
        } elseif (is_float($value) && is_nan($value)) {
            self::assertInstanceOf($expected::class, $actual);
            self::assertNan($actual->getValue());
        } else {
            self::assertEquals($expected, $actual);
        }
    }

    #[DataProvider('invalidTypesProvider')]
    public function testWriteInvalidValueThrowsException(TypeInterface $type): void
    {
        self::expectException(WriterException::class);
        $this->getSerializer()->write(self::createStub(StreamInterface::class), $type, $this->getWriter());
    }

    protected function getStream(): Stream
    {
        $resource = fopen('php://memory', 'r+');
        self::assertIsResource($resource);
        return new Stream($resource);
    }

    protected function getReader(): Reader
    {
        return new Reader();
    }

    protected function getWriter(): Writer
    {
        return new Writer();
    }
}

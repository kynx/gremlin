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
use function strlen;

abstract class AbstractSerializerTestCase extends TestCase
{
    protected const string CRYING = "\xF0\x9F\x98\xAD"; // loudly crying face

    protected ?StreamInterface $stream = null;

    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->stream instanceof StreamInterface) {
            $this->stream->close();
            $this->stream = null;
        }
    }

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
        $stream->write(self::CRYING);

        $this->getSerializer()->write($stream, $type, $this->getWriter());
        $stream->rewind();
        self::assertSame(self::CRYING, $stream->read(strlen(self::CRYING)));
        $actual = $stream->read(1024);
        self::assertSame($expected, $actual);
    }

    #[DataProvider('serializableTypesProvider')]
    public function testReadReturnsValue(TypeInterface $expected, string $bytes): void
    {
        $stream = $this->getStream($bytes, self::CRYING);
        $actual = $this->getSerializer()->read($stream, $this->getReader());

        /** @var mixed $value */
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

        self::assertSame(self::CRYING, $stream->read(1024));
    }

    #[DataProvider('invalidTypesProvider')]
    public function testWriteInvalidValueThrowsException(TypeInterface $type): void
    {
        self::expectException(WriterException::class);
        $this->getSerializer()->write(self::createStub(StreamInterface::class), $type, $this->getWriter());
    }

    protected function getStream(string ...$chunks): Stream
    {
        $resource = fopen('php://memory', 'r+');
        self::assertIsResource($resource);

        $this->stream = new Stream($resource);
        foreach ($chunks as $chunk) {
            $this->stream->write($chunk);
        }
        $this->stream->rewind();

        return $this->stream;
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

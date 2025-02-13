<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary\Serializer;

use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use Kynx\Gremlin\Structure\Io\Binary\Reader;
use Kynx\Gremlin\Structure\Io\Binary\Serializer\SerializerInterface;
use Kynx\Gremlin\Structure\Io\Binary\Writer;
use Kynx\Gremlin\Structure\Type\TypeInterface;
use KynxTest\Gremlin\Structure\Io\Binary\StreamTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

use function is_float;
use function is_nan;

abstract class AbstractSerializerTestCase extends TestCase
{
    use StreamTrait;

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->tearDownStream();
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
    public function testSerializeAppendsToStream(TypeInterface $type, string $expected): void
    {
        $stream = $this->getWrittenStream();
        $this->getSerializer()->serialize($stream, $type, $this->getWriter());
        self::assertWrittenStreamSame($expected, $stream);
    }

    #[DataProvider('serializableTypesProvider')]
    public function testUnSerializeReturnsValue(TypeInterface $expected, string $bytes): void
    {
        $stream = $this->getStream($bytes, self::CRYING);
        $actual = $this->getSerializer()->unserialize($stream, $this->getReader());

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

        self::assertHasRemainingStream($stream);
    }

    #[DataProvider('invalidTypesProvider')]
    public function testSerializeInvalidValueThrowsException(TypeInterface $type): void
    {
        self::expectException(DomainException::class);
        $this->getSerializer()->serialize(self::createStub(StreamInterface::class), $type, $this->getWriter());
    }

    protected function getReader(): Reader
    {
        return new Reader(...$this->getSerializers());
    }

    protected function getWriter(): Writer
    {
        return new Writer(...$this->getSerializers());
    }

    /**
     * @return array<SerializerInterface>
     */
    protected function getSerializers(): array
    {
        return [];
    }
}

<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Structure\Io\Binary;

use GuzzleHttp\Psr7\Stream;
use KynxTest\Gremlin\Structure\Io\Binary\Constraint\IsStreamContents;
use KynxTest\Gremlin\Structure\Io\Binary\Constraint\IsStreamRemaining;
use Psr\Http\Message\StreamInterface;

use function fopen;

/**
 * @require-extends \PHPUnit\Framework\TestCase
 */
trait StreamTrait
{
    protected const string CRYING = "\xF0\x9F\x98\xAD";

    protected ?StreamInterface $stream = null;

    protected function tearDownStream(): void
    {
        if ($this->stream instanceof StreamInterface) {
            $this->stream->close();
            $this->stream = null;
        }
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

    protected function getWrittenStream(): Stream
    {
        $stream = $this->getStream();
        $stream->write(self::CRYING);

        return $stream;
    }

    public static function assertStreamSame(string $expected, StreamInterface $actual, string $message = ''): void
    {
        self::assertThat($actual, new IsStreamContents($expected), $message);
    }

    public static function assertWrittenStreamSame(
        string $expected,
        StreamInterface $actual,
        string $message = ''
    ): void {
        self::assertThat($actual, new IsStreamContents(self::CRYING . $expected), $message);
    }

    public static function assertHasRemainingStream(StreamInterface $actual, string $message = ''): void
    {
        self::assertThat($actual, new IsStreamRemaining(self::CRYING), $message);
    }
}

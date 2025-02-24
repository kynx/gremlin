<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Driver\Stream;

use Exception;
use Kynx\Gremlin\Driver\Stream\TransportStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\WithoutErrorHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

use function restore_error_handler;
use function set_error_handler;

use const E_USER_WARNING;

#[CoversClass(TransportStream::class)]
final class TransportStreamTest extends TestCase
{
    private bool $restoreErrorHandler = false;

    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->restoreErrorHandler) {
            restore_error_handler();
        }
    }

    public function testCloseCallsListener(): void
    {
        $called   = false;
        $listener = static function () use (&$called): void {
            $called = true;
        };
        $delegate = $this->createMock(StreamInterface::class);
        $delegate->expects(self::once())
            ->method('close');

        $stream = new TransportStream($delegate, $listener);
        $stream->close();
        self::assertTrue($called);
    }

    public function testDestructCallsListener(): void
    {
        $called   = false;
        $listener = static function () use (&$called): void {
            $called = true;
        };
        $delegate = $this->createMock(StreamInterface::class);

        $stream = new TransportStream($delegate, $listener);
        unset($stream);
        self::assertTrue($called);
    }

    #[WithoutErrorHandler]
    public function testDestructRaisesWarningWhenListenerThrowsException(): void
    {
        $listener = static function (): void {
            throw new Exception('Foo');
        };
        $delegate = self::createStub(StreamInterface::class);

        $this->restoreErrorHandler = true;
        $triggered                 = null;
        set_error_handler(
            static function (int $code, string $message) use (&$triggered): void {
                $triggered = true;
                self::assertSame(E_USER_WARNING, $code);
                self::assertStringStartsWith('Error destroying ' . TransportStream::class . ': Foo', $message);
            }
        );

        $stream = new TransportStream($delegate, $listener);
        unset($stream);
        self::assertTrue($triggered);
    }

    #[DataProvider('methodProvider')]
    public function testMethodsAreDelegated(string $method, mixed $return, mixed ...$args): void
    {
        $delegate   = $this->createMock(StreamInterface::class);
        $invocation = $delegate->expects(self::once())
            ->method($method);
        if ($args !== []) {
            $invocation->with(...$args);
        }
        if ($return !== null) {
            $invocation->willReturn($return);
        }

        $stream = new TransportStream($delegate, null);
        /** @var bool|int|null|string $actual */
        $actual = $stream->$method(...$args);

        if ($return !== null) {
            self::assertSame($return, $actual);
        }
    }

    public static function methodProvider(): array
    {
        return [
            'close'       => ['close', null],
            'detach'      => ['detach', null],
            'getSize'     => ['getSize', 123],
            'tell'        => ['tell', 456],
            'eof'         => ['eof', true],
            'isSeekable'  => ['isSeekable', true],
            'seek'        => ['seek', null, 789],
            'rewind'      => ['rewind', null],
            'isWritable'  => ['isWritable', true],
            'write'       => ['write', 3, 'abc'],
            'read'        => ['read', 'def', 1024],
            'getContents' => ['getContents', 'ghi'],
            'getMetadata' => ['getMetadata', 'jkl', 'foo'],
            '__toString'  => ['__toString', 'mno'],
        ];
    }
}

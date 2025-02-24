<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Driver\Amphp;

use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Request;
use Exception;
use Kynx\Gremlin\Driver\Amphp\HttpTransport;
use Kynx\Gremlin\Driver\Exception\ClientException;
use Kynx\Gremlin\Driver\Exception\IdentityException;
use Kynx\Gremlin\Driver\Exception\ServerException;
use Kynx\Gremlin\Driver\TransportOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function current;

#[CoversClass(HttpTransport::class)]
final class HttpTransportTest extends TestCase
{
    public function testSubmitPassesHeadersToRequest(): void
    {
        $expected = "Custom header";
        $name     = "X-Custom";

        $response    = new MockResponse(200, [], '');
        $interceptor = new MockingInterceptor($response);
        $builder     = (new HttpClientBuilder())->intercept($interceptor);
        $transport   = new HttpTransport(new TransportOptions(), $builder);

        $transport->submit('http://example.com', [$name => $expected], '');
        $request = current($interceptor->requests);
        self::assertInstanceOf(Request::class, $request);

        $actual = $request->getHeader($name);
        self::assertSame($expected, $actual);
    }

    #[DataProvider('transportOptionProvider')]
    public function testSubmitPassesTransportOptionToRequest(
        string $property,
        string $method,
        float|int $expected
    ): void {
        $options     = new TransportOptions(...[$property => $expected]);
        $response    = new MockResponse(200, [], '');
        $interceptor = new MockingInterceptor($response);
        $builder     = (new HttpClientBuilder())->intercept($interceptor);
        $transport   = new HttpTransport($options, $builder);

        $transport->submit('http://example.com', [], '');
        $request = current($interceptor->requests);
        self::assertInstanceOf(Request::class, $request);

        $actual = $request->$method();
        self::assertSame($expected, $actual);
    }

    public static function transportOptionProvider(): array
    {
        return [
            'connect timeout'    => ['connectTimeout', 'getTcpConnectTimeout', 123.0],
            'handshake timeout'  => ['handshakeTimeout', 'getTlsHandshakeTimeout', 456.0],
            'inactivity timeout' => ['inactivityTimeout', 'getInactivityTimeout', 789.0],
            'transfer timeout'   => ['transferTimeout', 'getTransferTimeout', 987.0],
            'header size limit'  => ['headerSizeLimit', 'getHeaderSizeLimit', 123],
            'body size limit'    => ['bodySizeLimit', 'getBodySizeLimit', 456],
        ];
    }

    public function testSubmitReturnsContentInStream(): void
    {
        $expected  = "Foo bar baz";
        $transport = $this->getTransport(new MockResponse(body: $expected));

        $stream = $transport->submit('http://example.com', [], $expected);
        $actual = $stream->getContents();
        self::assertSame($expected, $actual);
    }

    public function testSubmitHandlesRequestException(): void
    {
        $exception = new Exception('Foo');
        $transport = $this->getTransport($exception);

        self::expectExceptionObject(ClientException::fromThrowable($exception));
        $transport->submit('http://example.com', [], '');
    }

    #[DataProvider('badStatusProvider')]
    public function testSubmitThrowsExceptionOnBadStatus(int $status, string $reason, RuntimeException $expected): void
    {
        $response  = new MockResponse($status, [], '', $reason);
        $transport = $this->getTransport($response);

        self::expectExceptionObject($expected);
        $transport->submit('http://example.com', [], '');
    }

    public static function badStatusProvider(): array
    {
        return [
            'status 401' => [401, "Unauthorized", IdentityException::unauthenticated('Unauthorized')],
            'status 403' => [403, "Forbidden", IdentityException::unauthorised('Forbidden')],
            'status 500' => [500, "Server Kaput", ServerException::fromResponse(500, 'Server Kaput')],
            'status 400' => [400, "Silly Rabbit", ClientException::fromResponse(400, 'Silly Rabbit')],
            'status 111' => [111, "Unlikely", ServerException::unexpectedResponse(111, 'Unlikely')],
        ];
    }

    private function getTransport(MockResponse|Exception $response): HttpTransport
    {
        $builder = (new HttpClientBuilder())->intercept(new MockingInterceptor($response));
        return new HttpTransport(new TransportOptions(), $builder);
    }
}

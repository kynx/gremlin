<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Driver\Amphp;

use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Interceptor\ModifyRequest;
use Amp\Http\Client\ParseException;
use Amp\Http\Client\Request;
use Kynx\Gremlin\Driver\Amphp\HttpTransport;
use Kynx\Gremlin\Driver\Exception\ClientException;
use Kynx\Gremlin\Driver\Exception\IdentityException;
use Kynx\Gremlin\Driver\Exception\ServerException;
use Kynx\Gremlin\Driver\TransportOptions;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function array_slice;
use function explode;
use function getenv;
use function json_decode;
use function json_validate;
use function microtime;
use function str_repeat;
use function strlen;

/**
 * These tests should be run against a local httpbin.org image:
 *
 * ```
 * docker run -p 80:80 kennethreitz/httpbin
 * ```
 *
 * You will need to create a local `phpunit.xml`:
 *
 * ```
 * cp phpunit.xml.dist phpunit.xml
 * ```
 *
 * Edit it and set the `HTTPBIN_URL` env var (to `http://localhost` if running on port 80, as above)
 */
#[CoversNothing]
#[Group('e2e')]
final class HttpTransportE2eTest extends TestCase
{
    private string $baseUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $env           = getenv();
        $this->baseUrl = $env['HTTPBIN_URL'] ?? '';
        if ($this->baseUrl === '') {
            self::fail("Environment variable 'HTTPBIN_URL' cannot be empty");
        }
    }

    public function testSubmitReturnsStream(): void
    {
        $expected  = str_repeat('z', 1024);
        $transport = new HttpTransport();

        $stream = $transport->submit($this->baseUrl . '/anything', [], $expected);
        /** @var bool|null|array $json */
        $json = json_decode($stream->getContents(), true);
        self::assertIsArray($json);
        self::assertArrayHasKey('data', $json);
        self::assertSame($expected, $json['data']);
    }

    #[DataProvider('badStatusProvider')]
    public function testSubmitBadStatusRaisesException(int $status, RuntimeException $expected): void
    {
        $transport = new HttpTransport();

        self::expectExceptionObject($expected);
        $transport->submit("$this->baseUrl/status/$status", [], '');
    }

    public static function badStatusProvider(): array
    {
        return [
            'status 401' => [401, IdentityException::unauthenticated('UNAUTHORIZED')],
            'status 403' => [403, IdentityException::unauthorised('FORBIDDEN')],
            'status 500' => [500, ServerException::fromResponse(500, 'INTERNAL SERVER ERROR')],
            'status 404' => [404, ClientException::fromResponse(404, 'NOT FOUND')],
            'status 103' => [103, ClientException::fromThrowable(new ParseException('Invalid status line: 0', 409))],
        ];
    }

    public function testSubmitProcessesChunks(): void
    {
        $expected  = 5;
        $builder   = (new HttpClientBuilder())->intercept($this->postToGetInterceptor());
        $transport = new HttpTransport(new TransportOptions(), $builder);

        $stream   = $transport->submit("$this->baseUrl/stream/$expected", [], "");
        $sections = array_slice(explode("\n", $stream->getContents()), 0, -1);
        self::assertCount($expected, $sections);
        foreach ($sections as $section) {
            self::assertTrue(json_validate($section), "Invalid json in chunk");
        }
    }

    public function testSubmitReadsAsynchronously(): void
    {
        // this should send 5 bytes at 1 second intervals after an initial 0.5s delay
        $url       = "$this->baseUrl/drip?duration=5&numbytes=5&code=200&delay=0.5";
        $builder   = (new HttpClientBuilder())->intercept($this->postToGetInterceptor());
        $transport = new HttpTransport(new TransportOptions(), $builder);

        $start  = microtime(true);
        $stream = $transport->submit($url, [], '');

        $byte = $stream->read(1);
        self::assertLessThan(1, microtime(true) - $start);
        self::assertSame(1, strlen($byte));

        $byte = $stream->read(1);
        self::assertGreaterThan(1, microtime(true) - $start);
        self::assertLessThan(2, microtime(true) - $start);
        self::assertSame(1, strlen($byte));

        $stream->close();
        $empty = $stream->read(1);
        self::assertSame('', $empty);
    }

    /**
     * httpbin.org does not support POST requests for /drip, /stream-bytes etc - sneakily convert them to GET
     */
    private function postToGetInterceptor(): ModifyRequest
    {
        return new ModifyRequest(static function (Request $request): Request {
            $request->setMethod('GET');
            return $request;
        });
    }
}

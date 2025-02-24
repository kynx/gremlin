<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Driver\Amphp;

use Amp\DeferredCancellation;
use Amp\Http\Client\HttpClient;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Request;
use GuzzleHttp\Psr7\PumpStream;
use Kynx\Gremlin\Driver\Exception\ClientException;
use Kynx\Gremlin\Driver\Exception\IdentityException;
use Kynx\Gremlin\Driver\Exception\ServerException;
use Kynx\Gremlin\Driver\Stream\TransportStream;
use Kynx\Gremlin\Driver\TransportInterface;
use Kynx\Gremlin\Driver\TransportOptions;
use Psr\Http\Message\StreamInterface;
use Throwable;

/**
 * @psalm-import-type HeaderParamArrayType from TransportInterface
 */
final readonly class HttpTransport implements TransportInterface
{
    private HttpClient $client;

    public function __construct(
        private TransportOptions $options = new TransportOptions(),
        ?HttpClientBuilder $builder = null
    ) {
        $this->client = $builder?->build() ?? HttpClientBuilder::buildDefault();
    }

    /**
     * @param HeaderParamArrayType $headers
     */
    public function submit(string $url, array $headers, string $payload): StreamInterface
    {
        $request      = $this->getRequest($url, $headers, $payload);
        $cancellation = new DeferredCancellation();

        try {
             $response = $this->client->request($request, $cancellation->getCancellation());
        } catch (Throwable $throwable) {
            throw ClientException::fromThrowable($throwable);
        }

        $status = $response->getStatus();
        if ($status > 199 && $status < 300) {
            return new TransportStream(
                new PumpStream(static fn (): string|false => $response->getBody()->read() ?? false),
                static function () use ($cancellation): void {
                    $cancellation->cancel();
                }
            );
        }

        $reason = $response->getReason();
        match (true) {
            $status === 401 => throw IdentityException::unauthenticated($reason),
            $status === 403 => throw IdentityException::unauthorised($reason),
            $status > 499   => throw ServerException::fromResponse($status, $reason),
            $status > 399   => throw ClientException::fromResponse($status, $reason),
            default         => throw ServerException::unexpectedResponse($status, $reason),
        };
    }

    /**
     * @param HeaderParamArrayType $headers
     */
    private function getRequest(string $url, array $headers, string $payload): Request
    {
        $request = new Request($url, 'POST', $payload);
        $request->setHeaders($headers);
        $request->setTcpConnectTimeout($this->options->connectTimeout);
        $request->setTlsHandshakeTimeout($this->options->handshakeTimeout);
        $request->setTransferTimeout($this->options->transferTimeout);
        $request->setInactivityTimeout($this->options->inactivityTimeout);
        $request->setHeaderSizeLimit($this->options->headerSizeLimit);
        $request->setBodySizeLimit($this->options->bodySizeLimit);

        return $request;
    }
}

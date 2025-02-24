<?php

declare(strict_types=1);

namespace KynxTest\Gremlin\Driver\Amphp;

use Amp\Cancellation;
use Amp\Http\Client\ApplicationInterceptor;
use Amp\Http\Client\DelegateHttpClient;
use Amp\Http\Client\Request;
use Amp\Http\Client\Response;
use Exception;
use PHPUnit\Framework\Assert;

use function array_shift;

final class MockingInterceptor extends Assert implements ApplicationInterceptor
{
    public array $requests;
    /** @var array<Exception|MockResponse>  */
    private array $responses;

    public function __construct(MockResponse|Exception ...$responses)
    {
        $this->requests  = [];
        $this->responses = $responses;
    }

    public function request(Request $request, Cancellation $cancellation, DelegateHttpClient $httpClient): Response
    {
        $this->requests[] = $request;
        self::assertNotEmpty($this->responses, "No more responses");
        $response = array_shift($this->responses);

        $cancellation->throwIfRequested();

        if ($response instanceof Exception) {
            throw $response;
        }

        return new Response(
            '1.1',
            $response->status,
            $response->reason,
            $response->headers,
            $response->body,
            $request
        );
    }
}

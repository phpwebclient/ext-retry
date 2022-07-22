<?php

declare(strict_types=1);

namespace Tests\Webclient\Extension\Retry;

use Psr\Http\Client\ClientExceptionInterface;
use Stuff\Webclient\Extension\Retry\Handler;
use Nyholm\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Webclient\Extension\Retry\RetryClientDecorator;
use Webclient\Extension\Retry\Timer\DefaultTimer;
use Webclient\Fake\FakeHttpClient;

use function array_reverse;
use function array_shift;
use function count;
use function json_decode;

class RetryClientTest extends TestCase
{
    public function testRetries()
    {
        $step = 50000;
        $badRequests = 4;
        $retries = 5;
        $handler = new Handler($badRequests);
        $baseHttpClient = new FakeHttpClient($handler);
        $timer = new DefaultTimer($step);
        $decoratedClient = new RetryClientDecorator($baseHttpClient, $timer, $retries);

        $request = new Request('GET', 'http://localhost', ['Accept' => 'text/plain']);

        $response = $decoratedClient->sendRequest($request);

        $json = $response->getBody()->__toString();
        $data = json_decode($json, true);

        self::assertSame($badRequests + 1, $data['successAttempt']);

        $timing = array_reverse($data['timing'], true);
        $k = count($timing) - 1;
        $last = array_shift($timing);
        foreach ($timing as $microtime) {
            $period = (int)(($last - $microtime) * 1000000);
            self::assertGreaterThan($timer->getDelayMicroSeconds($k), $period);
            $last = $microtime;
            $k--;
        }
    }

    public function testBadRetries()
    {
        $step = 1000;
        $badRequests = 5;
        $retries = 5;
        $handler = new Handler($badRequests);
        $baseHttpClient = new FakeHttpClient($handler);
        $timer = new DefaultTimer($step);
        $decoratedClient = new RetryClientDecorator($baseHttpClient, $timer, $retries);

        $request = new Request('GET', 'http://localhost', ['Accept' => 'text/plain']);

        $this->expectException(ClientExceptionInterface::class);
        $decoratedClient->sendRequest($request);
    }
}

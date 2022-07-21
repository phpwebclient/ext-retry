<?php

declare(strict_types=1);

namespace Webclient\Extension\Retry;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Webclient\Extension\Retry\Timer\DefaultTimer;
use Webclient\Extension\Retry\Timer\Timer;

use function is_null;
use function max;
use function usleep;

final class RetryClientDecorator implements ClientInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Timer|null
     */
    private $timer;

    /**
     * @var int
     */
    private $maxAttempts;

    public function __construct(ClientInterface $client, Timer $timer = null, int $maxAttempts = 3)
    {
        $this->client = $client;
        $this->timer = is_null($timer) ? new DefaultTimer() : $timer;
        $this->maxAttempts = max(1, $maxAttempts);
    }

    /**
     * @inheritDoc
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $attempt = 1;
        do {
            try {
                return $this->client->sendRequest($request);
            } catch (ClientExceptionInterface $exception) {
                $delay = $this->timer->getDelayMicroSeconds($attempt);
                $attempt++;
                usleep($delay);
            }
        } while ($attempt <= $this->maxAttempts);
        throw $exception;
    }
}

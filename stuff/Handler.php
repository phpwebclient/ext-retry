<?php

declare(strict_types=1);

namespace Stuff\Webclient\Extension\Retry;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function json_encode;
use function max;
use function microtime;

class Handler implements RequestHandlerInterface
{
    /**
     * @var int
     */
    private $badAttempts;

    /**
     * @var int
     */
    private $attempt = 0;

    /**
     * @var int[]
     */
    private $timing = [];

    public function __construct(int $badAttempts)
    {
        $this->badAttempts = max(1, $badAttempts);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->timing[$this->attempt] = microtime(true);
        $this->attempt += 1;
        if ($this->attempt <= $this->badAttempts) {
            throw new ClientException('unknown error');
        }
        $body = json_encode(['successAttempt' => $this->attempt, 'timing' => $this->timing]);
        return new Response(200, ['content-type' => 'text/plain'], $body);
    }
}

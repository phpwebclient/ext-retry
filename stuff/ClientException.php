<?php

declare(strict_types=1);

namespace Stuff\Webclient\Extension\Retry;

use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

class ClientException extends RuntimeException implements ClientExceptionInterface
{
}

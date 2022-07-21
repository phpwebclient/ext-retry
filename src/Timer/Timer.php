<?php

declare(strict_types=1);

namespace Webclient\Extension\Retry\Timer;

interface Timer
{
    public function getDelayMicroSeconds(int $attempt): int;
}

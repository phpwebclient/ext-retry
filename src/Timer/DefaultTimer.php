<?php

declare(strict_types=1);

namespace Webclient\Extension\Retry\Timer;

use function max;

final class DefaultTimer implements Timer
{
    /**
     * @var int
     */
    private $step;

    public function __construct(int $step = 100000)
    {
        $this->step = max(100, $step);
    }

    public function getDelayMicroSeconds(int $attempt): int
    {
        return $this->step * $attempt;
    }
}

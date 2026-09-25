<?php

namespace Nails\MFA\Driver\Authentication\Authenticator;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

/**
 * Wall clock for OTPHP. A null clock is deprecated since otphp 11.3 and removed in 12.
 */
class SystemClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}

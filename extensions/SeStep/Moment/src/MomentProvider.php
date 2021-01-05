<?php declare(strict_types=1);

namespace SeStep\Moment;

use DateTime;

interface MomentProvider
{
    public function now(): DateTime;
}

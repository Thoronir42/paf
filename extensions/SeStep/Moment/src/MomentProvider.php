<?php declare(strict_types=1);

namespace SeStep\Moment;

interface MomentProvider
{
    public function now(): \DateTime;
}

<?php declare(strict_types=1);

namespace SeStep\Moment;

use DateTime;

class RelativeMomentProvider implements MomentProvider
{
    /** @var DateTime */
    private $now;

    public function __construct(DateTime $now = null)
    {
        $this->now = $now ?: new DateTime();
    }

    public function now(): DateTime
    {
        return clone $this->now;
    }
}

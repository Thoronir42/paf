<?php declare(strict_types=1);

namespace PAF\Utils\Moment;


class MomentProvider
{
    /** @var \DateTime */
    private $now;

    public function __construct(\DateTime $now = null)
    {
        $this->now = $now ?: new \DateTime();
    }

    public function now(): \DateTime
    {
        return clone $this->now;
    }
}

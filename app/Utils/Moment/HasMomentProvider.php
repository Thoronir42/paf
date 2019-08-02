<?php declare(strict_types=1);

namespace PAF\Utils\Moment;

trait HasMomentProvider
{
    /** @var MomentProvider */
    private $momentProvider;

    /** @return MomentProvider */
    public function getMomentProvider(): MomentProvider
    {
        if (!$this->momentProvider) {
            $this->momentProvider = new MomentProvider(new \DateTime());
        }

        return $this->momentProvider;
    }

    /** @param MomentProvider $momentProvider */
    public function setMomentProvider(MomentProvider $momentProvider): self
    {
        $this->momentProvider = $momentProvider;

        return $this;
    }
}

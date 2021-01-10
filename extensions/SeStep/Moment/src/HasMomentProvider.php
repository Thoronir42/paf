<?php declare(strict_types=1);

namespace SeStep\Moment;

use DateTime;

trait HasMomentProvider
{
    private MomentProvider $momentProvider;

    /**
     * Retrieves MomentProvider instance
     *
     * @return MomentProvider
     */
    public function getMomentProvider(): MomentProvider
    {
        if (!isset($this->momentProvider)) {
            $this->momentProvider = new RelativeMomentProvider(new DateTime());
        }

        return $this->momentProvider;
    }

    /**
     * Injects momentProvider instance
     *
     * @param MomentProvider $momentProvider
     * @return self
     */
    public function setMomentProvider(MomentProvider $momentProvider): self
    {
        $this->momentProvider = $momentProvider;

        return $this;
    }
}

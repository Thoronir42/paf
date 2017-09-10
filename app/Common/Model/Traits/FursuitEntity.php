<?php

namespace App\Common\Model\Traits;

use App\Common\Model\Entity\PafWrapper;


/**
 * Trait Pafe
 * @package App\Common\Model\Traits
 *
 * @property PafWrapper $wrapper
 */
trait FursuitEntity
{
    /** @return PafWrapper */
    public function getWrapper() {
        return $this->wrapper;
    }

    /** @return string */
    public function getFeName() {
        return $this->wrapper->getName();
    }
}

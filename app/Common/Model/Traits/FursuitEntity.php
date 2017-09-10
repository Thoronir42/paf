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
    public function getFeName() {
        return $this->wrapper->getName();
    }
}

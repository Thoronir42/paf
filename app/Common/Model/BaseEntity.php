<?php declare(strict_types=1);

namespace PAF\Common\Model;

use LeanMapper\Entity;

abstract class BaseEntity extends Entity
{

    /**
     * @param $propertyName
     * @internal
     */
    public function unsetProperty($propertyName)
    {
        unset($this->row->$propertyName);
    }
}

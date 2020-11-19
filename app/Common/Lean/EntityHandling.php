<?php declare(strict_types=1);

namespace PAF\Common\Lean;

use LeanMapper\Entity;

trait EntityHandling
{
    public function setDefaults($data, bool $erase = false)
    {
        if ($data instanceof Entity) {
            $editableProperties = array_keys(iterator_to_array($this->getComponents()));
            $data =  $data->getData($editableProperties);
        }

        return parent::setDefaults($data, $erase);
    }
}

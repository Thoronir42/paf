<?php declare(strict_types=1);

namespace PAF\Common\Forms;

use LeanMapper\Entity;
use SeStep\NetteTypeful\Controls\TypefulUploadControl;

trait EntityHandling
{
    public function setDefaults($data, bool $erase = false)
    {
        static $SET_VALUE_EVEN_WHEN_SUBMITTED = [
            TypefulUploadControl::class
        ];

        if ($data instanceof Entity) {
            $editableProperties = array_keys(iterator_to_array($this->getComponents()));
            $data =  $data->getData($editableProperties);
        }

        $form = $this->getForm(false);
        if (!$form || !$form->isAnchored() || !$form->isSubmitted()) {
            $this->setValues($data, $erase);
        } else {
            $setWhenSubmitted = [];
            foreach ($data as $field => $value) {
                if (isset($this[$field]) && in_array(get_class($this[$field]), $SET_VALUE_EVEN_WHEN_SUBMITTED)) {
                    $setWhenSubmitted[$field] = $value;
                }
            }
            $this->setValues($setWhenSubmitted, $erase);
        }

        return $this;
    }
}

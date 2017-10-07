<?php

namespace App\Common\Forms;


use App\Common\Forms\Controls\DateInput;

trait FormExtender
{
    public function addDate($name, $label = null, $format = null) {
        return $this[$name] = new DateInput($label, $format);
    }

    /**
     * @param string $name
     * @return Container
     */
    public function addContainer($name)
    {
        $control = new Container();
        $control->currentGroup = $this->currentGroup;
        if ($this->currentGroup !== NULL) {
            $this->currentGroup->add($control);
        }

        return $this[$name] = $control;
    }
}

<?php declare(strict_types=1);

namespace PAF\Common\Forms;

use PAF\Common\Forms\Controls\DateInput;

trait FormCustomControls
{
    public function addDate(string $name, string $label = null): Controls\DateInput
    {
        return $this[$name] = new Controls\DateInput(DateInput::FORMAT_DATE, $label);
    }

    public function addDateTime(string $name, string $label = null): Controls\DateInput
    {
        return $this[$name] = new Controls\DateInput(DateInput::FORMAT_DATETIME, $label);
    }

    /**
     * @param string $name
     * @return Container
     */
    public function addContainer($name): \Nette\Forms\Container
    {
        $control = new Container();
        $control->currentGroup = $this->currentGroup;

        return $this[$name] = $control;
    }
}

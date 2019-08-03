<?php declare(strict_types=1);

namespace PAF\Common\Forms;

trait FormCustomControls
{
    public function addDate(string $name, string $label = null, $format = null): Controls\DateInput
    {
        return $this[$name] = new Controls\DateInput($label, $format);
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

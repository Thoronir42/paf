<?php declare(strict_types=1);

namespace SeStep\NetteBootstrap\Controls;

use Nette\Forms\Controls\Checkbox;
use Nette\Utils\Html;

class BootstrapCheckboxControl extends Checkbox
{
    public function getControl(): Html
    {
        $wrapper = Html::el('div', ['class' => 'custom-control custom-checkbox']);

        $checkBoxControl = $this->getControlPart();
        $checkBoxControl->class[] = 'custom-control-input';
        $wrapper->addHtml($checkBoxControl);

        $label = $this->getLabelPart();
        $label->class[] = 'custom-control-label';
        $wrapper->addHtml($label);

        return $wrapper;
    }
}

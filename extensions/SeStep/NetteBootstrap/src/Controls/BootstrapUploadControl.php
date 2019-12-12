<?php declare(strict_types=1);

namespace SeStep\NetteBootstrap\Controls;

use Nette\Forms\Controls\UploadControl;
use Nette\Utils\Html;

class BootstrapUploadControl extends UploadControl
{
    public function getControl()
    {
        $id = $this->getHtmlId();
        $input = parent::getControl();

        $wrapper = Html::el('div', ['class' => 'custom-file']);
        $wrapper->addHtml($input);

        $label = Html::el('label', [
            'class' => 'custom-file-label',
            'for' => $id,
        ]);
        $label->setText('Choose file');
        $wrapper->addHtml($label);

        return $wrapper;
    }
}

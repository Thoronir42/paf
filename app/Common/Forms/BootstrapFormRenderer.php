<?php declare(strict_types=1);

namespace PAF\Common\Forms;

use Nette;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Rendering\DefaultFormRenderer;
use Nette\Utils\Html;

class BootstrapFormRenderer extends DefaultFormRenderer
{
    public function __construct()
    {
        $this->wrappers = $this->createWrappers();
    }


    public function render(Nette\Forms\Form $form, string $mode = null): string
    {
        $form->getElementPrototype()->appendAttribute('class', 'bs-form');

        return parent::render($form, $mode);
    }

    private function createWrappers()
    {
        return [
            'form' => [
                'container' => null,
            ],

            'error' => [
                'container' => 'ul class=error',
                'item' => 'li',
            ],

            'group' => [
                'container' => 'fieldset',
                'label' => 'legend',
                'description' => 'p',
            ],

            'controls' => [
                'container' => null,
            ],

            'pair' => [
                'container' => 'div class="row form-group"',
                '.required' => 'required',
                '.optional' => null,
                '.odd' => null,
                '.error' => 'has-error',
                '.button' => 'button',
            ],

            'control' => [
                'container' => 'div class="col-sm-12 col-md-9"',
                '.odd' => null,

                'description' => 'span class=help-block',
                'requiredsuffix' => '',
                'errorcontainer' => 'span class=form-text text-danger',
                'erroritem' => '',

                '.required' => 'required',
                '.text' => 'form-control',
                '.number' => 'form-control',
                '.numeric' => 'form-control',
                '.select' => 'form-control',
                '.password' => 'form-control',
                '.submit' => 'btn btn-primary pull-right',
                '.image' => 'imagebutton',
                '.button' => 'button',
            ],

            'label' => [
                'container' => 'div class="col-sm-12 col-md-3"',
                'suffix' => null,
                'requiredsuffix' => '',
            ],

            'hidden' => [
                'container' => 'div',
            ],
        ];
    }

    public function renderPair(Nette\Forms\IControl $control): string
    {
        if (!($control instanceof Nette\Forms\Controls\BaseControl)) {
            return parent::renderPair($control);
        }

        $pair = $this->getWrapper('pair container');
        $pair->addHtml($this->renderLabel($control));
        $controlHtml = $this->renderControl($control);
        if ($help = $control->getOption('help-text')) {
            $controlHtml->addHtml('<p class="form-text text-muted">' . $control->translate($help) . '</p>');
        }
        $pair->addHtml($controlHtml);

        $pair->class($this->getValue($control->isRequired() ? 'pair .required' : 'pair .optional'), true);
        $pair->class($control->hasErrors() ? $this->getValue('pair .error') : null, true);
        $pair->class($control->getOption('class'), true);
        if (++$this->counter % 2) {
            $pair->class($this->getValue('pair .odd'), true);
        }
        $pair->id = $control->getOption('id');

        return $pair->render(0);
    }

    public function renderControl(Nette\Forms\IControl $control): Html
    {
        if ($control instanceof TextArea) {
            $control->getControlPrototype()->class[] = 'form-control';
        }

        return parent::renderControl($control);
    }
}

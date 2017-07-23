<?php

namespace App\Common\Controls\Forms;

use Nette\Forms\Controls\TextArea;
use Nette\Forms\Form;
use Nette\Forms\Rendering\DefaultFormRenderer;

class BootstrapFormRenderer extends DefaultFormRenderer
{
	public function __construct()
	{
        $this->wrappers = $this->createWrappers();
	}
	

	public function render(Form $form, $mode = NULL)
	{
		foreach ($form->getControls() as $control) {
		    if($control instanceof TextArea) {
                $control->getControlPrototype()->class[] = 'form-control';
		    }

		}

		return parent::render($form, $mode);
	}

	private function createWrappers()
	{
		return [
			'form' => [
				'container' => NULL,
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
				'.optional' => NULL,
				'.odd' => NULL,
				'.error' => 'has-error',
				'.button' => 'button',
			],

			'control' => [
				'container' => 'div class=col-sm-9',
				'.odd' => NULL,

				'description' => 'span class=help-block',
				'requiredsuffix' => '',
				'errorcontainer' => 'span class=help-block error',
				'erroritem' => '',

				'.required' => 'required',
				'.text' => 'form-control',
				'.number' => 'form-control',
				'.select' => 'form-control',
				'.password' => 'text',
				'.file' => 'btn btn-default btn-file',
				'.submit' => 'btn btn-default',
				'.image' => 'imagebutton',
				'.button' => 'button',
			],

			'label' => [
				'container' => 'div class="col-sm-3 control-label"',
				'suffix' => NULL,
				'requiredsuffix' => '',
			],

			'hidden' => [
				'container' => 'div',
			],
		];
	}


}

<?php

namespace App\Forms;

use Nette\Forms\Rendering\DefaultFormRenderer;

class BootstrapFormRenderer extends DefaultFormRenderer
{
	public function __construct()
	{
		$this->setWrappers();
	}
	
/*
	public function render(\Nette\Forms\Form $form, $mode = NULL)
	{
		foreach ($form->getControls() as $control) {
			$control->getControlPrototype()->class[] = $control->name;
		}

		return parent::render($form, $mode);
	}
*/
	private function setWrappers()
	{
		$wrappers = [
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
				'container' => 'div class="form-group"',
				'.required' => 'required',
				'.optional' => NULL,
				'.odd' => NULL,
				'.error' => 'has-error',
				'.button' => 'button',
			],

			'control' => [
				'container' => NULL,
				'.odd' => NULL,

				'description' => 'small',
				'requiredsuffix' => '',
				'errorcontainer' => 'span class=error',
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
				'container' => NULL,
				'suffix' => NULL,
				'requiredsuffix' => '',
			],

			'hidden' => [
				'container' => 'div',
			],
		];

		$this->wrappers = $wrappers;

	}


}

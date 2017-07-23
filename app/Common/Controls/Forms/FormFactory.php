<?php

namespace App\Common\Controls\Forms;

use Nette;
use Nette\Application\UI\Form;


class FormFactory extends Nette\Object
{

	/**
	 * @return Form
	 */
	public function create()
	{
		$form = new Form();

		$form->setRenderer(new BootstrapFormRenderer());
		$form->getElementPrototype()->class = 'bs-form';
		
		return $form;
	}

}

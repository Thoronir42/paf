<?php

namespace App\Forms;

use Nette\Application\UI as UI;
use Nette\Application\UI\Form;


/**
 * Class BaseFormControl
 * @package App\Forms
 *
 * @method onSave(Form $form, $values)
 */
abstract class BaseFormControl extends UI\Control
{
	/** @var callable[]  function (Form $form, ArrayHash $result); Occurs when form successfully validates input. */
	public $onSave;

	/** @var FormFactory */
	protected $factory;

	public function __construct(FormFactory $factory)
	{
		parent::__construct();
		$this->factory = $factory;
	}

	/**
	 * @return mixed
	 */
	public abstract function createComponentForm();

	public abstract function processForm(Form $form, $values);

	/** @return Form */
	protected function form(){
		return $this['form'];
	}
}

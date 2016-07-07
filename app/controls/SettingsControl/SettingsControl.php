<?php

namespace App\Controls\Settings;


use App\Forms\BaseFormControl;
use App\Forms\FormFactory;
use App\Model\Settings\AOption;
use App\Model\Settings\OptionBool;
use App\Model\Settings\OptionInt;
use App\Model\Settings\OptionString;
use Nette\Application\UI\Form;

class SettingsControl extends BaseFormControl
{
	/** @var AOption */
	private $option;

	public function __construct(AOption $option, FormFactory $factory)
	{
		parent::__construct($factory);
		$this->option = $option;
	}

	public function renderForm(){
		$this->template->setFile(__DIR__ . '/settingsControlForm.latte');

		$this->template->render();
	}

	public function createComponentForm()
	{
		$form = $this->factory->create();

		if($this->option instanceof OptionString) {
			$form->addText('value', 'Hodnota');
		} elseif($this->option instanceof OptionInt){
			$form->addText('value', 'Hodnota')->getControlPrototype()->type = 'number';
		} elseif($this->option instanceof OptionBool){
			$form->addCheckbox('value', 'Hodnota');
		} else {
			$this->flashMessage('Invalid option type.');
			return $form;
		}

		$form->addSubmit('save', 'Save!');

		return $form;
	}

	public function processForm(Form $form, $values)
	{
		// TODO: Implement processForm() method.
	}
}

interface ISettingsControlFactory
{
	/**
	 * @param AOption $option
	 * @return SettingsControl
	 */
	public function create(AOption $option);
}

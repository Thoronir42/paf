<?php

namespace App\Forms;


use Nette;
use Nette\Application\UI as UI;


use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\User;

class SignInForm extends BaseFormControl
{
	public function render()
	{
		$this->template->setFile(__DIR__ . '/signInForm.latte');
		$this->template->render();
	}

	public function createComponentForm()
	{
		$form = $this->factory->create();
		
		$form->addText('login', 'Uživatelské jméno')
			->setRequired();

		$form->addPassword('password', 'Heslo')
			->setRequired();

		$form->addCheckbox('remember', 'Pamatovat si mě');

		$form->addSubmit('send', 'Odeslat');

		$form->onSuccess[] = $this->processForm;
		return $form;
	}


	public function processForm(Form $form, $values)
	{
		$this->onSave($form, $values);
	}
}

interface ISignInFormFactory
{
	/** @return SignInForm */
	function create();
}

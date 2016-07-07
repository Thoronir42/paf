<?php

namespace App\Presenters;

use Nette;
use App\Forms\ISignInFormFactory;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Utils\DateTime;


class SignPresenter extends BasePresenter
{
	/** @var ISignInFormFactory @inject */
	public $in_factory;

	public function actionDefault()
	{
		$this->redirect('in');
	}

	public function actionOut()
	{
		$this->getUser()->logout(true);
		$this->redirect('Default:');
	}

	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = $this->in_factory->create();

		$form->onSave[] = function (Form $form, $values){
			$login = $values->login;
			$password = $values->password;
			$remember = $values->remember;
			
			if ($remember) {
				$this->user->setExpiration('14 days', FALSE);
			} else {
				$this->user->setExpiration('30 minutes', TRUE);
			}

			try {
				$this->user->login($login, $password);
			} catch (AuthenticationException $e) {
				$form->addError('Nesprávné jméno nebo heslo.');
				return;
			}
			$this->redirect('Default:');
		};

		return $form;
	}

	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignUpForm()
	{
		$form = $this->up_factory->create();

		$form->onSave[] = function (Form $form, $values){
			$login = $values->login;
			$password = $values->password;

			$check = $this->users->findOneBy(['username' => $login]);
			if($check){
				$form->addError('Uživatel se jménem' . $login . ' již existuje.');
				return;
			}

			$this->users->createNewUser($login, $password);

			$this->flashMessage('Váš účet byl vytvořen.');

			$this->redirect('in');
		};

		return $form;
	}

}

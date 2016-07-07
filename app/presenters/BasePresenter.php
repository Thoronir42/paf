<?php

namespace App\Presenters;

use App\Controls\NavigationMenu\INavigationMenuFactory;
use App\Model\Entity\Quote;
use App\Model\Entity\User;
use App\Model\Services\Quotes;
use App\Model\Services\Users;
use Nette;
use App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	/** @var  INavigationMenuFactory @inject */
	public $navigationMenuFactory;

	/** @var Users @inject  */
	public $users;
	/** @var Quotes @inject  */
	public $debtProfiles;

	/** @var User  */
	protected $eUser;
	/** @var Quote  */
	protected $profile;

	public function startup()
	{
		parent::startup();

		$this->template->appName = 'Pretty Acceptable Fursuits';
		$this->template->title = '';

		if($this->user->isLoggedIn()){
			$this->eUser = $this->users->find($this->user->id);
		}
	}

	protected function authenticationCheck($message = 'Pro vstup do této části je přihlášení nezbytné.', $allowedActions = ['default']){
		$action = $this->getAction();
		if(in_array($action, $allowedActions) || $this->user->isLoggedIn()){
			return;
		}

		$this->flashMessage($message);
		if(empty($allowedActions)){
			$this->redirect('Default:');
		}

		$action = array_shift($allowedActions);
		$this->redirect($action);
	}

	public function createComponentMenu()
	{
		$menu = $this->navigationMenuFactory->create();
		$menu->setTitle('Debtr');
		if(true || $this->user->isLoggedIn()){
			$debts = $menu->addItem('Default:', 'Wowie');
			$debts->addItem('Quotes:', 'Pls?');

		}


		/*
		if($this->user->isLoggedIn()){
			$manageItem = $menu->addItem('default', 'Správa');
			$manageItem->addItem('Games:add', 'Zadat novou hru');
			$manageItem->addSeparator();
			$manageItem->addItem('Platforms:', 'Platformy');
			$manageItem->addItem('States:', 'Stavy');
			$manageItem->addItem('Tags:', 'Tagy');
		}
		*/
		return $menu;
	}
}

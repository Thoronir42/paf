<?php

namespace App\Presenters;

use App\Model\Entity\User;
use App\Services\Doctrine\Users;
use Nette;
use App\Model;
use SeStep\Navigation\Control\INavigationMenuFactory;
use SeStep\Settings\Settings;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    /** @var  INavigationMenuFactory @inject */
    public $navigationMenuFactory;

    /** @var Users @inject */
    public $users;
    /** @var Settings @inject */
    public $settings;
    /** @var User */
    protected $eUser;

    public function startup()
    {
        parent::startup();

        $this->template->appName = $this->context->parameters['appName'];
        $this->template->background_color = '#25c887';
        $this->template->title = '';

        if ($this->user->isLoggedIn()) {
            $this->eUser = $this->users->find($this->user->id);
        }
    }

    protected function authenticationCheck(
        $message = 'Pro vstup do této části je přihlášení nezbytné.',
        $allowedActions = ['default']
    ) {
        $action = $this->getAction();
        if (in_array($action, $allowedActions) || $this->user->isLoggedIn()) {
            return;
        }

        $this->flashMessage($message);
        if (empty($allowedActions)) {
            $this->redirect('Default:');
        }

        $action = array_shift($allowedActions);
        $this->redirect($action);
    }

    public function createComponentMenu()
    {
        $menu = $this->navigationMenuFactory->create();

        $menu->setTitle($this->context->parameters['appName']);
        if (true || $this->user->isLoggedIn()) {
            $quotes = $menu->addLink('Quotes:', 'Su-meme-ry');
            $quotes->addLink('Quotes:', 'Pls?');
        }

        return $menu;
    }
}

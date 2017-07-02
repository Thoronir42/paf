<?php

namespace App\Common;


use App\Common\Model\Entity\User;
use App\Common\Services\Doctrine\Users;
use App\Modules\Admin\Presenters\SettingsPresenter;
use Nette\Application\UI\Presenter;
use SeStep\Navigation\Control\INavigationMenuFactory;
use SeStep\SettingsInterface\Settings;

abstract class BasePresenter extends Presenter
{

    /** @var Users @inject */
    public $users;
    /** @var Settings @inject */
    public $settings;

    /** @var  INavigationMenuFactory @inject */
    public $navigationMenuFactory;


    /** @var User */
    protected $eUser;

    protected function startup()
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
            $this->redirect(':Front:Default:');
        }

        $action = array_shift($allowedActions);
        $this->redirect($action);
    }

    public function formatLayoutTemplateFiles()
    {
        $fileCandidates = parent::formatLayoutTemplateFiles();
        array_unshift($fileCandidates, __DIR__ . '/templates/@layout.latte');

        return $fileCandidates;
    }

    public function createComponentMenu()
    {
        $menu = $this->navigationMenuFactory->create();

        $menu->setBrandTarget(':Front:Default:');
        $menu->setTitle($this->context->parameters['appName']);

        $menu->addLink(':Front:Quotes:', 'Quotes');

        if ($this->user->isAllowed(SettingsPresenter::class)) {
            $manage = $menu->addLink(':Admin:Settings:', 'Manage');
            $manage->addLink(':Admin:Settings:', 'Settings');
            $manage->addLink(':Admin:Cases:list', 'Cases');
        }

        return $menu;
    }


}

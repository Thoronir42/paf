<?php

namespace App\Common;


use App\Common\Controls\Footer\Footer;
use App\Common\Model\Entity\User;
use App\Common\Services\Doctrine\Users;
use App\Modules\Admin\Presenters\SettingsPresenter;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;
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

    /** @var Translator @inject */
    public $translator;

    /** @var User */
    protected $eUser;

    protected function startup()
    {
        parent::startup();
        
        /** @var Template $template */
        $template = $this->template;
        $template->setTranslator($this->translator->domain(strtolower(str_replace(":", ".", $this->name))));

        $this->template->defaultTranslator = $this->translator;

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

        $menu->addLink(':Front:Quotes:', 'paf.views.quotes');

        if ($this->user->isAllowed(SettingsPresenter::class)) {
            $manage = $menu->addLink(':Admin:Settings:', 'paf.views.manage');
            $manage->addLink(':Admin:Settings:', 'paf.views.settings');
            $manage->addLink(':Admin:Cases:list', 'paf.views.cases');
        }

        return $menu;
    }

    public function createComponentFooter()
    {
        return new Footer();
    }


}

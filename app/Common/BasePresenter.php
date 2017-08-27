<?php

namespace App\Common;


use App\Common\Auth\Authorizator;
use App\Common\Controls\Footer\Footer;
use App\Common\Controls\NavigationMenu\INavigationMenuFactory;
use App\Common\Model\Entity\User;
use App\Common\Services\Doctrine\Users;
use App\Modules\Admin\Presenters\SettingsPresenter;
use Kdyby\Translation\ITranslator;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;
use SeStep\SettingsInterface\Settings;

/**
 * Class BasePresenter
 * @package App\Common
 *
 * @property-read Template $template
 */
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

        $domain = strtolower(str_replace(":", ".", $this->name));
        $this->setTranslator($this->translator->domain($domain));

        $this->template->defaultTranslator = $this->translator;

        $this->template->appName = $this->context->parameters['appName'];
        $this->template->background_color = '#25c887';
        $this->template->title = '';

        if ($this->user->isLoggedIn()) {
            $this->eUser = $this->users->find($this->user->id);
        }
    }


    protected function validateAuthorization($resource, $privilege = Authorizator::ALL, $redirect = null)
    {
        if ($this->user->isAllowed($resource, $privilege)) {
            return true;
        }

        $msgArgs = ['resource' => $this->translator->translate('auth.resource.' . $resource)];
        if ($privilege) {
            $msgArgs['privilege'] = $this->translator->translate('auth.privilege.' . $privilege);
            $message = $this->translator->translate('auth.resource-privilege-unauthorized', $msgArgs);
        } else {
            $message = $this->translator->translate('auth.resource-unauthorized', $msgArgs);
        }
        $this->flashMessage($message);

        if (!$redirect || $this->presenter->isLinkCurrent($redirect)) {
            $this->redirect(':Front:Default:');
            return false;
        }

        $this->redirect($redirect);
        return false;
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

        if ($this->user->isAllowed('admin-settings')) {
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


    protected function setTranslator(ITranslator $translator)
    {
        /** @var Template $template */
        $template = $this->template;
        $template->setTranslator($translator);
    }

}

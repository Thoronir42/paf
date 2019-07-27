<?php declare(strict_types=1);

namespace PAF\Common;

use Nette\Application\UI\ITemplate;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Localization\ITranslator;
use PAF\Common\Security\Authorizator;
use PAF\Modules\CommonModule\Components\NavigationMenu\NavigationMenu;
use PAF\Modules\CommonModule\Model\User;
use PAF\Modules\CommonModule\Repository\UserRepository;
use SeStep\GeneralSettings\Settings;
use stdClass;

/**
 * Class BasePresenter
 * @package PAF\Common
 *
 * @property-read Template|stdClass $template
 */
abstract class BasePresenter extends Presenter
{

    /** @var string @persistent */
    public $lang = 'en';


    /** @var UserRepository @inject */
    public $users;

    /** @var Settings @inject */
    public $settings;

    /** @var ITranslator @inject */
    public $translator;

    /** @var User */
    protected $eUser;

    protected function startup()
    {
        parent::startup();

        $domain = strtolower(str_replace(":", ".", $this->name));
        $this->template->setTranslator($this->translator->domain($domain));

        $this->template->defaultTranslator = $this->translator;

        $this->template->appName = $this->context->parameters['appName'];
        $this->template->background_color = '#25c887';
        $this->template->title = '';

        // todo: move this to LiveUserStorage
        if ($this->user->isLoggedIn()) {
            $this->eUser = $this->users->find($this->user->id);
        }
    }

    protected function createTemplate(): ITemplate
    {
        $template = parent::createTemplate();
        $template->lang = $this->lang;

        return $template;
    }

    // todo: rewrite into (?)PresenterAuthorizator
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
            $this->redirect(':Common:Homepage:default');
            return false;
        }

        $this->redirect($redirect);
        return false;
    }

    public function formatLayoutTemplateFiles(): array
    {
        $fileCandidates = parent::formatLayoutTemplateFiles();
        array_unshift($fileCandidates, __DIR__ . '/templates/@layout.latte');

        return $fileCandidates;
    }

    public function createComponentMenu()
    {
        $menu = new NavigationMenu($this->translator);

        $menu->setBrandTarget(':Common:Homepage:default');
        $menu->setTitle($this->context->parameters['appName']);

        $menu->addLink(':Quote:Quotes:default', 'paf.views.quotes');

        if ($this->user->isAllowed('admin-settings')) {
            $manage = $menu->addLink(':Admin:Settings:', 'paf.views.manage');
            $manage->addLink(':Admin:Settings:', 'paf.views.settings');
            $manage->addLink(':Admin:Cases:list', 'paf.views.cases');
        }

        return $menu;
    }


    /**
     * @param string $placeholder
     * @param array|string $variables
     * @param string $level
     * @return stdClass
     */
    protected function flashTranslate($placeholder, $variables = [], $level = 'info')
    {
        if (is_string($variables)) {
            $level = $variables;
            $variables = [];
        }

        return $this->flashMessage($this->translator->translate($placeholder, null, $variables), $level);
    }
}

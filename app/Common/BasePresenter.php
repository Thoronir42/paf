<?php declare(strict_types=1);

namespace PAF\Common;

use Nette\Application\UI\ITemplate;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Localization\ITranslator;
use PAF\Common\Security\Authorizator;
use PAF\Modules\SettingsModule\Components\SettingsControl\OptionNodeControl;
use PAF\Modules\SettingsModule\InlineOption\SettingsOptionAccessor;
use SeStep\GeneralSettings\Settings;
use SeStep\NavigationMenuComponent\NavigationMenu;
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

    /** @var Settings @inject */
    public $settings;

    /** @var ITranslator @inject */
    public $translator;

    protected function startup()
    {
        parent::startup();

        $this->template->appName = $this->context->parameters['appName'];
        $this->template->background_color = '#25c887';
        $this->template->title = '';
    }

    protected function createTemplate(): ITemplate
    {
        $template = parent::createTemplate();
        $template->setTranslator($this->translator);
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
        /** @var NavigationMenu $navMenu */
        $navMenu = $this->context->getService('navigationMenu.control');

        return $navMenu;
    }

    public function createComponentOption()
    {
        $optionAccessor = new SettingsOptionAccessor($this->settings);
        $optionAccessor->onValueChanged[] = function ($fqn) {
            if ($this->isAjax()) {
                $this->sendJson([
                    'status' => 'success'
                ]);
            } else {
                $this->flashMessage("Value of $fqn changed");
            }
        };
        $optionAccessor->onError[] = function($ex) {
            $this->sendJson([
                'status' => 'error',
                'message' => get_class($ex) . ': ' . $ex->getMessage(),
                'source' => $ex->getFile() . ':' . $ex->getLine(),
            ]);
        };

        return new OptionNodeControl($optionAccessor, '', $this->translator);
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

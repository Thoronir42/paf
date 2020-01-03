<?php declare(strict_types=1);

namespace PAF\Common;

use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Localization\ITranslator;
use PAF\Common\Security\ReflectionAuthorizator;
use PAF\Modules\SettingsModule\Components\SettingsControl\OptionNodeControl;
use PAF\Modules\SettingsModule\InlineOption\SettingsOptionAccessor;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use SeStep\GeneralSettings\Settings;
use stdClass;

/**
 * Class BasePresenter
 * @package PAF\Common
 *
 * @property-read Template|stdClass $template
 */
abstract class BasePresenter extends Presenter implements LoggerAwareInterface
{
    use Logging\HasLogger;

    /** @var string @persistent */
    public $lang = 'en';

    /** @var Settings @inject */
    public $settings;

    /** @var ITranslator @inject */
    public $translator;

    /** @var ReflectionAuthorizator @inject */
    public $reflectionAuthorizator;


    protected function createTemplate(): ITemplate
    {
        $template = parent::createTemplate();
        $template->setTranslator($this->translator);

        $template->lang = $this->lang;
        $template->appName = $this->context->parameters['appName'];
        $template->title = '';

        return $template;
    }

    public function checkRequirements($element): void
    {
        parent::checkRequirements($element);
        if ($element instanceof \ReflectionMethod) {
            $result = $this->reflectionAuthorizator->checkMethod($element);
            if (!$result->isValid()) {
                $args = [
                    'resource' => $this->translator->translate('auth.resource.' . $result->getResource()),
                ];
                if (($privilege = $result->getPrivilege())) {
                    $args['privilege'] = $this->translator->translate('auth.privilege.' . $privilege);
                }

                $message = $this->translator->translate($result->getMessage(), $args);
                throw new ForbiddenRequestException($message);
            }
        }
    }

    public function formatLayoutTemplateFiles(): array
    {
        $fileCandidates = parent::formatLayoutTemplateFiles();
        array_unshift($fileCandidates, __DIR__ . '/templates/@layout.latte');

        return $fileCandidates;
    }

    public function createComponentNavbar()
    {
        return $this->context->getService('paf.navbar');
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
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
        $optionAccessor->onError[] = function ($ex) {
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

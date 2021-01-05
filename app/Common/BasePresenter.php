<?php declare(strict_types=1);

namespace PAF\Common;

use Nette\Application\ForbiddenRequestException;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Localization\ITranslator;
use PAF\Common\Security\ReflectionAuthorizator;
use PAF\Modules\DirectoryModule\Services\HasAppUser;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

use PAF\Modules\Settings\SettingsAccess;
use SeStep\NetteBootstrap\Controls\Menu\Navbar;
use stdClass;

/**
 * @property-read Template|stdClass $template
 */
abstract class BasePresenter extends Presenter implements LoggerAwareInterface
{
    use Logging\HasLogger;
    use HasAppUser;
    use SettingsAccess;

    /** @persistent */
    public string $lang = 'en';

    /** @inject */
    public ITranslator $translator;

    /** @inject */
    public ReflectionAuthorizator $reflectionAuthorizator;


    protected function createTemplate(): ITemplate
    {
        $template = parent::createTemplate();
        $template->setTranslator($this->translator);

        $template->lang = $this->lang;
        $template->appName = $this->settings->getValue('common.appName');
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
        /** @var Navbar $navBar */
        $navBar = $this->context->getService('paf.navbar');
        if ($this->dirPerson) {
            $navBar->setUserName($this->appUser->username);
        }
        return $navBar;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $placeholder
     * @param array|string $variables
     * @param string $level
     * @return stdClass
     */
    protected function flashTranslate(string $placeholder, $variables = [], $level = 'info'): \stdClass
    {
        if (is_string($variables)) {
            $level = $variables;
            $variables = [];
        }

        return $this->flashMessage($this->translator->translate($placeholder, null, $variables), $level);
    }
}

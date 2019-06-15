<?php declare(strict_types=1);

namespace PAF\Modules\SettingsModule\Components\SettingsControl;


use Nette\Application\UI;
use Nette\Application\UI\Multiplier;
use Nette\Bridges\ApplicationLatte\Template;
use RuntimeException;
use SeStep\GeneralSettings\Options\IOptionSection;

/**
 * @method onSetValue($name, $value)
 *
 * @property Template $template
 */
class SettingsControl extends UI\Control
{
    /** @var callback[] */
    public $onSetValue = [];

    /** @var int */
    private $maxNesting;

    /** @var IOptionSection */
    private $section;

    public function __construct(int $maxNesting)
    {
        $this->maxNesting = $maxNesting;
    }

    public function setSection(IOptionSection $section) {
        $this->section = $section;
    }

    public function renderBootstrap()
    {
        $this->template->class = 'default';

        $this->template->fqn = $fqn = $this->section->getFQN();
        $this->template->control_name = str_replace(IOptionSection::DOMAIN_DELIMITER, '-', $fqn);
        $this->template->caption = $this->section->getCaption();

        $this->template->setFile(__DIR__ . '/settingsControlBootstrap.latte');
        $this->template->render();
    }

    public function createComponentOptions()
    {
        return new OptionNodeControl($this->section);
    }

    public function handleSet($name, $value)
    {
        $this->onSetValue($name, $value);
    }
}

interface ISettingsControlFactory
{
    public function create(int $maxNesting = 5): SettingsControl;
}

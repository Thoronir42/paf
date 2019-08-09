<?php declare(strict_types=1);

namespace PAF\Modules\SettingsModule\Components\SettingsControl;

use Nette\Application\UI;
use Nette\ComponentModel\IComponent;
use SeStep\GeneralSettings\Model\IOption;
use SeStep\GeneralSettings\Model\IOptionSection;

/**
 * @method onSetValue($name, $value)
 * @method onExpand(string $fqn)
 */
class SettingsControl extends UI\Control
{
    /** @var callback[] */
    public $onSetValue = [];

    /** @var callable[] */
    public $onExpand = [];

    /** @var int */
    private $expandDepth;

    /** @var IOptionSection */
    private $section;

    public function __construct(IOptionSection $section, int $expandDepth)
    {
        $this->section = $section;
        $this->expandDepth = $expandDepth;
    }

    public function renderBootstrap()
    {
        $this->template->class = 'default';

        $this->template->section = $this->section;
        $this->template->canExpandSubSections = $this->canExpandSubSections();

        $this->template->setFile(__DIR__ . '/settingsControlBootstrap.latte');
        $this->template->render();
    }

    public function getOptions(IOptionSection $section)
    {
        return array_filter($section->getNodes(), function ($node) {
            return $node instanceof IOption;
        });
    }

    public function getSubSections(IOptionSection $section)
    {
        return array_filter($section->getNodes(), function ($node) {
            return $node instanceof IOptionSection;
        });
    }

    public function createComponent($name): ?IComponent
    {
        $node = $this->section->getNode($name);
        if ($node instanceof IOption) {
            $optionNodeControl = new OptionNodeControl($node);
            $optionNodeControl->onSetValue = &$this->onSetValue;
            return $optionNodeControl;
        }
        if ($node instanceof IOptionSection && $this->canExpandSubSections()) {
            $settingsControl = new SettingsControl($node, $this->expandDepth - 1);
            $settingsControl->onExpand = &$this->onExpand;
            $settingsControl->onSetValue = &$this->onSetValue;

            return $settingsControl;
        }

        return null;
    }

    public function handleExpand(string $fqn)
    {
        $this->onExpand($fqn);
    }

    public function canExpandSubSections(): bool
    {
        return $this->expandDepth > 0;
    }

    public function handleSet($name, $value)
    {
        $this->onSetValue($name, $value);
    }
}

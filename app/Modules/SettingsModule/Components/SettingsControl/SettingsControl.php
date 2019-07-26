<?php declare(strict_types=1);

namespace PAF\Modules\SettingsModule\Components\SettingsControl;


use Nette\Application\UI;
use Nette\Application\UI\Multiplier;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\ComponentModel\IComponent;
use RuntimeException;
use SeStep\GeneralSettings\Options\IOption;
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
    private $expandDepth;

    /** @var IOptionSection */
    private $section;

    public function __construct(IOptionSection $section, int $expandDepth = 2)
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
        if($node instanceof IOption) {
            return new OptionNodeControl($node);
        }
        if($node instanceof IOptionSection && $this->canExpandSubSections()) {
            return new SettingsControl($node, $this->expandDepth - 1);
        }

        return null;
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

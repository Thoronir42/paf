<?php declare(strict_types=1);

namespace PAF\Modules\SettingsModule\Components\SettingsControl;

use Nette\Application\UI;
use Nette\ComponentModel\IComponent;
use Nette\InvalidArgumentException;
use Nette\Localization\ITranslator;
use Nette\Utils\Html;
use PAF\Modules\SettingsModule\InlineOption\OptionAccessor;
use SeStep\GeneralSettings\DomainLocator;
use SeStep\GeneralSettings\Exceptions\NodeNotFoundException;
use SeStep\GeneralSettings\Exceptions\OptionNotFoundException;
use SeStep\GeneralSettings\Exceptions\SectionNotFoundException;
use SeStep\GeneralSettings\Model\INode;
use SeStep\GeneralSettings\Model\IOption;
use SeStep\GeneralSettings\Model\IOptionSection;

/**
 * Class OptionControl
 *
 * @package SeStep\SettingsControl
 */
final class OptionNodeControl extends UI\Control implements OptionAccessor
{
    /** @var ITranslator */
    private $translator;

    /** @var OptionAccessor */
    private $optionAccessor;
    /** @var string */
    private $optionFqn;

    public function __construct(OptionAccessor $optionAccessor, string $optionFqn, ITranslator $translator = null)
    {
        $this->optionAccessor = $optionAccessor;
        $this->optionFqn = $optionFqn;
        $this->translator = $translator;
    }

    public function render()
    {
        $node = $this->getNode($this->optionFqn);
        if (!$node) {
            throw new NodeNotFoundException($this->optionFqn);
        }

        if ($node instanceof IOptionSection) {
            $this->renderSectionTemplate($node);
        } elseif ($node instanceof IOption) {
            echo $node->getValue();
        } else {
            throw new \UnexpectedValueException('Node type ' . $node->getType() . ' could not be rendered');
        }
    }

    public function renderEditable()
    {
        $node = $this->getNode($this->optionFqn);
        if (!$node instanceof IOption) {
            throw new OptionNotFoundException($this->optionFqn, $node);
        }

        $el = $this->createElement($node);

        $el->data('url-set', $this->link('set!'));
        $el->data('value-name', $this->getUniqueId() . '-value');

        echo $el->render();
    }

    public function renderLabel()
    {
        $text = $this->translator ? $this->translator->translate($this->optionFqn) : $this->optionFqn;

        echo '<label class="option-inline-label" title="' . $this->optionFqn . '">' . $text . '</label>';
    }

    public function renderPair(bool $editable = true)
    {
        echo '<div class="option-inline-pair">';
        $this->renderLabel();
        $this->renderEditable();
        echo '</div>';
    }

    public function renderSection()
    {
        $node = $this->getNode($this->optionFqn);
        if (!$node instanceof IOptionSection) {
            throw new SectionNotFoundException($this->optionFqn, $node);
        }

        $this->renderSectionTemplate($node);
    }

    private function renderSectionTemplate(IOptionSection $section)
    {
        $this->template->section = $section;
        // todo: replace by more sophisticated check
        $this->template->canExpandSubSections = count(explode(INode::DOMAIN_DELIMITER, $this->optionFqn)) <= 2;

        $childNodes = $section->getNodes();
        $this->template->options = array_filter($childNodes, function ($node) {
            return $node instanceof IOption;
        });
        $this->template->subSections = array_filter($childNodes, function ($node) {
            return $node instanceof IOptionSection;
        });

        $this->template->setFile(__DIR__ . '/optionNodeControl-section.latte');
        $this->template->render();
    }

    public function handleListAvailableValues()
    {
        $node = $this->getNode($this->optionFqn);
        if (!$node instanceof IOption) {
            throw new \UnexpectedValueException("Node must be an IOption");
        }

        if ($node->hasValuePool()) {
            $values = $node->getValuePool()->getValues();
        } elseif ($node->getType() === IOption::TYPE_BOOL) {
            $values = [1 => 'yes', 0 => 'no'];
        } else {
            return ['status' => 'error', 'message' => 'This option does not support values'];
        }

        $editableValues = [];
        foreach ($values as $value => $label) {
            $editableValues[] = [
                'value' => $value,
                'text' => $label,
            ];
        }

        return $editableValues;
    }

    public function handleSet($value = null)
    {
        $this->optionAccessor->setValue($this->optionFqn, $value);
    }

    public function createComponent($name): ?IComponent
    {
        $childFqn = $this->optionFqn ? DomainLocator::concatFQN($name, $this->optionFqn) : $name;

        $node = $this->getNode($childFqn);
        if (!$node) {
            throw new NodeNotFoundException($childFqn);
        }

        return new OptionNodeControl($this->optionAccessor, $childFqn, $this->translator);
    }

    private function createElement(IOption $option): Html
    {
        $el = Html::el('a');

        $el->attrs['class'][] = 'editable';

        $value = $option->getValue();
        $type = $option->getType();

        $el->data('pk', $option->getFQN());
        $el->data('value', $this->getEditableValue($value, $type));
        $el->data('type', $this->getEditableType($type));
        $el->setText($this->getValueText($value, $type));

        if ($option->hasValuePool() || $type === IOption::TYPE_BOOL) {
            $el->data('source', $this->link('listAvailableValues!'));
        }

        return $el;
    }

    private function getEditableType($optionType)
    {
        switch ($optionType) {
            case IOption::TYPE_INT:
                return 'number';
            case IOption::TYPE_STRING:
                return 'text';
            case IOption::TYPE_BOOL:
                return 'select';
        }

        throw new InvalidArgumentException("Unknown type '$optionType'");
    }

    // todo: separe into transformer-thingy
    private function getEditableValue($value, $type)
    {
        switch ($type) {
            case IOption::TYPE_BOOL:
                return $value ? 1 : 0;
        }

        return $value;
    }

    private function getValueText($value, $type)
    {
        switch ($type) {
            default:
                return $value;

            case IOption::TYPE_BOOL:
                $label = $value ? "yes" : "no";
                if ($this->translator) {
                    $label = $this->translator->translate($label);
                }

                return $label;
        }
    }

    public function getNode(string $fqn): ?INode
    {
        return $this->optionAccessor->getNode($fqn);
    }

    public function setValue(string $fqn, $value): bool
    {
        return $this->optionAccessor->setValue($fqn, $value);
    }
}

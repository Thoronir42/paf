<?php declare(strict_types=1);

namespace PAF\Modules\SettingsModule\Components\SettingsControl;

use Nette\Application\UI;
use Nette\InvalidArgumentException;
use Nette\Localization\ITranslator;
use Nette\Utils\Html;
use SeStep\GeneralSettings\Options\IOption;

/**
 * Class OptionControl
 *
 * @package SeStep\SettingsControl
 *
 * @method onSetValue(string $name, $value)
 */
class OptionNodeControl extends UI\Control
{
    /** @var callable[] */
    public $onSetValue = [];

    /** @var IOption */
    private $node;

    /** @var ITranslator */
    private $translator;


    public function __construct(IOption $node)
    {
        $this->node = $node;
    }

    public function render()
    {
        $el = $this->createElement($this->node);

        $el->data('url-set', $this->link('set!'));
        $el->data('value-name', $this->getUniqueId() . '-value');

        echo $el->render();
    }

    public function handleListAvailableValues()
    {
        if ($this->node->hasValuePool()) {
            $values = $this->node->getValuePool()->getValues();
        } elseif ($this->node->getType() === IOption::TYPE_BOOL) {
            $values = [1 => 'yes', 0 => 'no'];
        } else {
            $this->presenter->sendJson(['status' => 'error', 'message' => 'This option does not support values']);
            return;
        }

        $editableValues = [];
        foreach ($values as $value => $label) {
            $editableValues[] = [
                'value' => $value,
                'text' => $label,
            ];
        }

        $this->presenter->sendJson($editableValues);
    }

    public function handleSet($value = null)
    {
        $this->onSetValue($this->node->getFQN(), $value);
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
}

<?php declare(strict_types=1);

namespace PAF\Common\Forms\Controls;

use Nette\Forms\Controls\TextInput;
use Nette\UnexpectedValueException;
use Nette\Utils\DateTime;
use Nette\Utils\Html;

class DateInput extends TextInput
{
    const FORMAT_DATE = "Y-m-d";
    const FORMAT_DATETIME = "Y-m-d H:i";

    protected $format;

    public function __construct(string $format, string $label = null)
    {
        parent::__construct($label);
        $this->setOption('type', 'datetime');

        $this->format = $format;
        $this->setRequired(false);
        $this->addRule([$this, 'validateDate'], 'invalid-date-format');
    }

    public function setDate(DateTime $value)
    {
        return $this->setValue($value->format($this->format));
    }

    /**
     * @param string|DateTime $value
     * @return static
     * @internal
     */
    public function setValue($value)
    {
        if ($value instanceof \DateTime) {
            $value = $value->format($this->format);
        }
        /** @noinspection PhpInternalEntityUsedInspection */
        parent::setValue($value);
        return $this;
    }

    public function getValue()
    {
        return DateTime::createFromFormat($this->format, parent::getValue());
    }


    public function getControl(): Html
    {
        $element = parent::getControl();
        $id = 'td-' . $this->lookupPath();

        $element->class[] = 'form-control datetimepicker-input';
        $element->data('target', "#$id");


        $wrapper = Html::el('div', [
            'class' => 'input-group date td-wrapper',
            'id' => $id,
        ]);
        $wrapper->data('target-input', 'nearest');
        $wrapper->data('format', $this->getBootstrapFormat($this->format));

        $wrapper->addHtml($element);
        $wrapper->addHtml($this->createPickerAddOn("#$id"));

        return $wrapper;
    }

    private function getBootstrapFormat($format)
    {
        switch ($format) {
            case self::FORMAT_DATE:
                return 'YYYY-MM-DD';
            case self::FORMAT_DATETIME:
                return 'YYYY-MM-DD HH:mm';
        }

        throw new UnexpectedValueException("Format $format is not supported");
    }

    private function createPickerAddOn(string $targetSelector): Html
    {
        $inputAddon = Html::el('div');
        $inputAddon->class[] = 'input-group-append';
        $inputAddon->data('target', $targetSelector);
        $inputAddon->data('toggle', 'datetimepicker');

        $inputAddon->addHtml('<div class="input-group-text"><i class="fa fa-calendar"></i></div>');

        return $inputAddon;
    }

    public function validateDate(DateInput $input)
    {
        $stringValue = parent::getValue();

        if (!$stringValue) {
            return true;
        }

        return $this->getValue() instanceof \DateTime;
    }
}

<?php declare(strict_types=1);

namespace PAF\Common\Forms\Controls;

use Nette\Forms\Controls\TextInput;
use Nette\UnexpectedValueException;
use Nette\Utils\DateTime;
use Nette\Utils\Html;
use SeStep\NetteBootstrap as NBS;

class DateInput extends TextInput
{
    const FORMAT_DATE = "Y-m-d";
    const FORMAT_DATETIME = "Y-m-d H:i";

    protected string $format;

    public function __construct(string $format, string $label = null)
    {
        parent::__construct($label);
        $this->setOption('type', 'datetime');

        $this->format = $format;
        $this->setRequired(false);
        $this->addRule([$this, 'validateDate'], 'invalid-date-format');
    }

    public function setDate(DateTime $value): self
    {
        return $this->setValue($value->format($this->format));
    }

    /**
     * @param string|DateTime $value
     * @return static
     * @internal
     */
    public function setValue($value): self
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
        $value = DateTime::createFromFormat($this->format, parent::getValue());
        if ($this->format === self::FORMAT_DATE) {
            $value->setTime(0, 0, 0);
        }

        return $value;
    }


    public function getControl(): Html
    {
        $element = parent::getControl();
        $id = 'td-' . $this->lookupPath();

        $element->class[] = 'form-control datetimepicker-input';
        $element->data('target', "#$id");

        $inputGroup = new NBS\InputGroup($element);
        $inputGroup->class[] = 'date td-wrapper';
        $inputGroup->id = $id;
        $inputGroup->data('target-input', 'nearest');
        $inputGroup->data('format', $this->getBootstrapFormat($this->format));

        $append = $inputGroup->append(NBS\InputGroup::text('<i class="fa fa-calendar"></i>'));
        $append->data('target', "#$id");
        $append->data('toggle', 'datetimepicker');

        return $inputGroup;
    }

    private function getBootstrapFormat($format): string
    {
        switch ($format) {
            case self::FORMAT_DATE:
                return 'YYYY-MM-DD';
            case self::FORMAT_DATETIME:
                return 'YYYY-MM-DD HH:mm';
        }

        throw new UnexpectedValueException("Format $format is not supported");
    }

    public function validateDate(DateInput $input): bool
    {
        $stringValue = parent::getValue();

        if (!$stringValue) {
            return true;
        }

        return $this->getValue() instanceof \DateTime;
    }
}

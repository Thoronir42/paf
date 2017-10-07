<?php

namespace App\Common\Forms\Controls;


use Nette\Forms\Controls\TextInput;
use Nette\UnexpectedValueException;
use Nette\Utils\DateTime;

class DateInput extends TextInput
{
    const FORMAT_DATE = "Y-m-d";
    const FORMAT_DATETIME = "Y-m-d H:i";

    const VIEW_MINUTE = 0;
    const VIEW_HOUR = 1;
    const VIEW_DAY = 2;
    const VIEW_MONTH = 3;
    const VIEW_YEAR = 4;
    const VIEW_DECADE = 5;

    const POSITION_BOTTOM_RIGHT = 'bottom-right';
    const POSITION_BOTTOM_LEFT = 'bottom-left';
    const POSITION_TOP_RIGHT = 'top-right';
    const POSITION_TOP_LEFT = 'top-left';

    protected $format;
    protected $bsFormat;

    protected $position;

    public function __construct($label = null, $format = null)
    {
        parent::__construct($label);
        $this->setOption('type', 'datetime');

        $this->format = $format ?: self::FORMAT_DATE;
        $this->setRequired(false);
        $this->addRule([$this, 'validateDate'], 'invalid-date-format');
    }

    public function setPickerPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    public function setDate(DateTime $value)
    {
        return $this->setValue($value->format($this->format));
    }

    /**
     * @param string|DateTime $value
     * @return static
     */
    public function setValue($value)
    {
        if ($value instanceof \DateTime) {
            $value = $value->format($this->format);
        }
        parent::setValue($value);
        return $this;
    }

    public function getValue()
    {
        return DateTime::createFromFormat($this->format, parent::getValue());
    }


    public function getControl()
    {
        $element = parent::getControl();
        $view = $this->getMinView($this->format);
        $attrs = [
            'data-date-format' => $this->getBootstrapFormat($this->format),
            'data-min-view'    => $view,
            'data-start-view'  => self::VIEW_DAY,
        ];
        if ($this->position) {
            $attrs['data-picker-position'] = $this->position;
        }
        $element->addAttributes($attrs);

        return $element;
    }

    private function getBootstrapFormat($format)
    {
        switch ($format) {
            case self::FORMAT_DATE:
                return 'yyyy-mm-dd';
            case self::FORMAT_DATETIME:
                return 'yyyy-mm-dd hh:ii';
        }

        throw new UnexpectedValueException("Format $format is not supported");
    }

    private function getMinView($format)
    {
        switch ($format) {
            case self::FORMAT_DATE:
                return self::VIEW_DAY;
            case self::FORMAT_DATETIME:
                return self::VIEW_MINUTE;
        }

        return -1;
    }

    public function validateDate(DateInput $input)
    {
        $stringValue = parent::getValue();

        if(!$stringValue) {
            return true;
        }

        return $this->getValue() instanceof \DateTime;
    }


}

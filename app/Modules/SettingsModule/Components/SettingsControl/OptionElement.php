<?php declare(strict_types=1);

namespace PAF\Modules\SettingsModule\Components\SettingsControl;


use Nette\NotImplementedException;
use Nette\Utils\Html;
use SeStep\GeneralSettings\Options\IOption;

class OptionElement extends Html
{
    const
        TYPE_SELECT = 'select',
        TYPE_TEXT = 'text',
        TYPE_INT = 'number';

    static private $renderTypes = [
        IOption::TYPE_BOOL => self::TYPE_SELECT,
        IOption::TYPE_STRING => self::TYPE_TEXT,
        IOption::TYPE_INT => self::TYPE_INT,
    ];

    private $type;

    public function __construct(IOption $option)
    {
        $this->type = $this->findRenderType($option->getType());

        $this->setName('a');
        $this->attrs['class'][] = 'editable';

        $this->data('value', $option->getValue());
        $this->data('pk', $option->getFQN());
        $this->data('type', $this->type);

        $this->setText($this->humanifyValue($option->getValue(), $option->getType()));
    }


    /**
     * @param $type
     * @return string
     */
    private function findRenderType($type)
    {
        if (!isset(self::$renderTypes[$type])) {
            throw new NotImplementedException("No render type declared for option of type $type");
        }

        return self::$renderTypes[$type];
    }

    private function humanifyValue($value, $type)
    {
        switch ($type) {
            default:
                return $value;
            case IOptions::TYPE_BOOL:
                // todo: use YesNoFilter
                $filter = function ($value) {
                    return $value ? "Yes" : "No";
                };
                break;
        }

        return $filter($value);
    }
}

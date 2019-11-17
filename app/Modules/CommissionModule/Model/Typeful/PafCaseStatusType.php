<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Model\Typeful;

use Nette\Localization\ITranslator;
use SeStep\Typeful\PropertyType;

class PafCaseStatusType extends PropertyType
{
    /** @var ITranslator */
    private $translator;

    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    public function renderValue($value, array $options = [])
    {
        return $this->translator->translate('commission.case.status.' . $value);
    }

    public static function getName(): string
    {
        return 'commission.case.status';
    }
}

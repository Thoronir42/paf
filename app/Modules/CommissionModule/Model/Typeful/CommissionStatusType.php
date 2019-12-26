<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Model\Typeful;

use Nette\Localization\ITranslator;
use SeStep\Typeful\PropertyType;

class CommissionStatusType extends PropertyType
{
    /** @var ITranslator */
    private $translator;

    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    public function renderValue($value, array $options = [])
    {
        return $this->translator->translate('commission.commission.status.' . $value);
    }

    public static function getName(): string
    {
        return 'commission.commission.status';
    }
}

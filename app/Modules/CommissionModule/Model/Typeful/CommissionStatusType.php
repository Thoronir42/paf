<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Model\Typeful;

use Nette\Localization\ITranslator;
use SeStep\Typeful\Types\PropertyType;
use SeStep\Typeful\Types\RendersValue;
use SeStep\Typeful\Validation\ValidationError;

class CommissionStatusType implements PropertyType, RendersValue
{
    private ITranslator $translator;

    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    public function renderValue($value, array $options = [])
    {
        return $this->translator->translate('commission.commission.status.' . $value);
    }

    public function validateValue($value, array $options = []): ?ValidationError
    {
        return null;
    }
}

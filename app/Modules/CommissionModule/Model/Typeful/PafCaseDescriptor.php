<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Model\Typeful;

use SeStep\Typeful\EntityDescriptor;
use SeStep\Typeful\Property;

class PafCaseDescriptor implements EntityDescriptor
{
    /**
     * @var Property[]
     */
    private $properties;

    public function __construct()
    {
        $this->properties = [
            'status' => new Property('status', 'commission.case.status'),
            'targetDelivery' => new Property('targetDelivery', 'datetime'),
        ];
    }

    public function getProperty(string $name): ?Property
    {
        return $this->properties[$name] ?? null;
    }

    public function getPropertyFullName(string $property): ?string
    {
        if (!isset($this->properties[$property])) {
            return null;
        }

        return 'commission.case.' . $property;
    }
}

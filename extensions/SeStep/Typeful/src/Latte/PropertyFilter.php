<?php declare(strict_types=1);

namespace SeStep\Typeful\Latte;

use Nette\Localization\ITranslator;
use SeStep\Typeful\TypeRegister;

class PropertyFilter
{

    /** @var TypeRegister */
    private $typeRegister;

    /**
     * PropertyFilter constructor.
     *
     * @param TypeRegister $typeRegister
     */
    public function __construct(TypeRegister $typeRegister)
    {
        $this->typeRegister = $typeRegister;
    }

    public function displayPropertyName(string $property, string $entityClass = null)
    {
        $descriptor = $this->typeRegister->getEntityDescriptor($entityClass);

        return $descriptor->getPropertyFullName($property);
    }

    public function displayEntityProperty($value, string $entityClass, string $propertyName, array $options = [])
    {
        $property = $this->typeRegister->getEntityProperty($entityClass, $propertyName);
        $propertyType = $property ? $this->typeRegister->getPropertyType($property->getType()) : null;

        if (!$propertyType) {
            trigger_error("Property '$entityClass::$propertyName'' can not be displayed");
            return 'nada';
        }

        return $propertyType->renderValue($value, $options);
    }
}

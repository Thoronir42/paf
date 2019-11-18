<?php declare(strict_types=1);

namespace SeStep\Typeful\Latte;

use SeStep\Typeful\EntityDescriptorRegistry;
use SeStep\Typeful\TypeRegister;

class PropertyFilter
{

    /** @var TypeRegister */
    private $typeRegister;
    /** @var EntityDescriptorRegistry */
    private $entityDescriptorRegistry;

    /**
     * PropertyFilter constructor.
     *
     * @param TypeRegister $typeRegister
     * @param EntityDescriptorRegistry $entityDescriptorRegistry
     */
    public function __construct(TypeRegister $typeRegister, EntityDescriptorRegistry $entityDescriptorRegistry)
    {
        $this->typeRegister = $typeRegister;
        $this->entityDescriptorRegistry = $entityDescriptorRegistry;
    }

    public function displayPropertyName(string $property, string $entityClass = null)
    {
        $descriptor = $this->entityDescriptorRegistry->getEntityDescriptor($entityClass);

        return $descriptor->getPropertyFullName($property);
    }

    public function displayEntityProperty($value, string $entityType, string $propertyName, array $options = [])
    {
        $descriptor = $this->entityDescriptorRegistry->getEntityDescriptor($entityType);
        if (!$descriptor) {
            trigger_error("Entity $entityType not recognized");
            return 'nada';
        }
        $property = $descriptor->getProperty($propertyName);
        $propertyType = $property ? $this->typeRegister->getPropertyType($property->getType()) : null;

        if (!$propertyType) {
            trigger_error("Property '$entityType::$propertyName'' can not be displayed");
            return 'nada';
        }

        return $propertyType->renderValue($value, $options);
    }
}

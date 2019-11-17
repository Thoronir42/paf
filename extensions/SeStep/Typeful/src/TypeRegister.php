<?php declare(strict_types=1);

namespace SeStep\Typeful;

use Nette\InvalidStateException;
use UnexpectedValueException;

class TypeRegister
{
    /** @var PropertyType[] */
    private $propertyTypes;

    /** @var EntityDescriptor[] */
    private $entityDescriptors = [];

    /**
     * TypeRegister constructor.
     *
     * @param PropertyType[] $propertyTypes
     */
    public function __construct(array $propertyTypes)
    {
        foreach ($propertyTypes as $type) {
            $this->propertyTypes[$type::getName()] = $type;
        }
    }

    public function registerDescriptor(string $class, EntityDescriptor $descriptor)
    {
        if (isset($this->entityDescriptors[$class])) {
            throw new UnexpectedValueException("Entity descriptor for class '$class' is already registered");
        }

        $this->entityDescriptors[$class] = $descriptor;
    }

    public function getEntityDescriptor(string $entityClass): EntityDescriptor
    {
        $descriptor = $this->entityDescriptors[$entityClass] ?? null;
        if (!$descriptor) {
            throw new InvalidStateException("Entity '$entityClass' is not registered");
        }

        return $descriptor;
    }

    public function getEntityProperty(string $entityClass, string $propertyName): ?Property
    {
        $descriptor = $this->getEntityDescriptor($entityClass);
        return $descriptor->getProperty($propertyName);
    }

    public function getPropertyType(string $type): ?PropertyType
    {
        if (!isset($this->propertyTypes[$type])) {
            trigger_error("Property type '$type' is not recognized");
            return null;
        }

        return $this->propertyTypes[$type];
    }
}

<?php declare(strict_types=1);

namespace SeStep\Typeful;

use Nette\InvalidStateException;

class EntityDescriptorRegistry
{
    private $descriptors = [];

    public function __construct(array $descriptors)
    {
        foreach ($descriptors as $entityType => $descriptor) {
            $this->add($entityType, $descriptor);
        }
    }

    public function getEntityDescriptor(string $entityClass): ?EntityDescriptor
    {
        return $this->descriptors[$entityClass] ?? null;
    }

    public function getEntityProperty(string $entityClass, string $propertyName): ?Property
    {
        $descriptor = $this->getEntityDescriptor($entityClass);
        if (!$descriptor) {
            throw new InvalidStateException("Entity '$entityClass' is not registered");
        }

        return $descriptor->getProperty($propertyName);
    }

    private function add(string $entityType, EntityDescriptor $descriptor)
    {
        if (isset($this->descriptors[$entityType])) {
            throw new InvalidStateException("Entity descriptor for class '$entityType' is already registered");
        }

        $this->descriptors[$entityType] = $descriptor;
    }
}

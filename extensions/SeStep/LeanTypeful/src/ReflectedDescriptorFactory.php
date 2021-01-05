<?php declare(strict_types=1);

namespace SeStep\LeanTypeful;

use LeanMapper\IMapper;
use LeanMapper\Reflection\EntityReflection;
use Nette\Caching\Cache;
use SeStep\Typeful\Entity\GenericDescriptor;
use SeStep\Typeful\Entity\Property;

class ReflectedDescriptorFactory
{
    private IMapper $mapper;
    private ReflectionProvider $provider;
    private Cache $cache;

    public function __construct(IMapper $mapper, ReflectionProvider $provider, Cache $cache)
    {
        $this->mapper = $mapper;
        $this->provider = $provider;
        $this->cache = $cache;
    }

    public function create(
        string $entityClass,
        array $reflectionArguments,
        array $propertiesArguments,
        string $propertyNamePrefix = ''
    ) {

        $inferredArguments = $this->inferPropertyTypes($entityClass, $reflectionArguments);
        $propertiesArguments = $this->mergePropertyArguments($propertiesArguments, $inferredArguments);

        $properties = [];
        foreach ($propertiesArguments as $name => $property) {
            ['type' => $type, 'options' => $options] = $property;
            $properties[$name] = new Property($type, $options);
        }

        return new GenericDescriptor($properties, $propertyNamePrefix);
    }

    private function inferPropertyTypes(string $entityClass, $reflectionArguments)
    {
        /** @var EntityReflection $entityReflection */
        $entityReflection = $entityClass::getReflection($this->mapper);
        /** @var \LeanMapper\Reflection\Property[] $propertiesByColumn */
        $propertiesByColumn = [];
        foreach ($entityReflection->getEntityProperties() as $name => $property) {
            $propertiesByColumn[$property->getColumn()] = $property;
        }

        return $this->cache->load(
            [$entityClass, $reflectionArguments],
            function () use ($entityClass, $reflectionArguments, $propertiesByColumn) {
                $table = $this->mapper->getTable($entityClass);

                $propertiesWhitelist = $reflectionArguments['properties'] ?? [];

                $columnWhitelist = array_map(function ($propertyName) use ($entityClass) {
                    return $this->mapper->getColumn($entityClass, $propertyName);
                }, $propertiesWhitelist);

                $columnTypes = $this->provider->getColumns($table, $columnWhitelist);

                $propertyTypes = [];
                foreach ($columnTypes as $columnName => $type) {
                    $property = $propertiesByColumn[$columnName] ?? null;
                    if (!$property) {
                        throw new \ReflectionException("Column '$columnName' does not correspond to a property");
                    }
                    $propertyTypes[$property->getName()] = $type;
                }

                return $propertyTypes;
            }
        );
    }

    private function mergePropertyArguments(array $staticArguments, $inferredArguments)
    {
        $arguments = [];

        foreach ($staticArguments as $propertyName => $staticConfig) {
            $arguments[$propertyName] = $this->mergeProperty($staticConfig, $inferredArguments[$propertyName] ?? null);
        }

        foreach (array_diff_key($inferredArguments, $staticArguments) as $name => $type) {
            $arguments[$name] = $type;
        }

        return $arguments;
    }

    private function mergeProperty(array $staticConfig, array $inferredConfig = null): array
    {
        if (!$inferredConfig) {
            return $staticConfig;
        }


        foreach ($inferredConfig['options'] as $option => $optionValue) {
            if (isset($staticConfig['options'][$option])) {
                continue;
            }
            $staticConfig['options'][$option] = $optionValue;
        }

        return $staticConfig;
    }
}

<?php declare(strict_types=1);

namespace SeStep\LeanTypeful;

use LeanMapper\IMapper;
use Nette\Caching\Cache;
use SeStep\Typeful\Entity\GenericDescriptor;
use SeStep\Typeful\Entity\Property;

class ReflectedDescriptorFactory
{
    /** @var IMapper */
    private $mapper;
    /** @var ReflectionProvider */
    private $provider;
    /** @var Cache */
    private $cache;

    public function __construct(IMapper $mapper, ReflectionProvider $provider, Cache $cache)
    {
        $this->mapper = $mapper;
        $this->provider = $provider;
        $this->cache = $cache;
    }

    public function create(
        string $table,
        array $reflectionArguments,
        array $propertiesArguments,
        string $propertyNamePrefix = ''
    ) {

        $inferredArguments = $this->inferPropertyTypes($table, $reflectionArguments);
        $propertiesArguments = $this->mergePropertyArguments($propertiesArguments, $inferredArguments);

        $properties = [];
        foreach ($propertiesArguments as $name => $property) {
            ['type' => $type, 'options' => $options] = $property;
            $properties[$name] = new Property($type, $options);
        }

        return new GenericDescriptor($properties, $propertyNamePrefix);
    }

    private function inferPropertyTypes(string $table, $reflectionArguments)
    {
        return $this->cache->load([$table, $reflectionArguments], function () use ($table, $reflectionArguments) {
            $entityClass = $this->mapper->getEntityClass($table);
            $propertiesWhitelist = $reflectionArguments->properties ?? [];

            $columnWhitelist = array_map(function ($propertyName) use ($entityClass) {
                return $this->mapper->getColumn($entityClass, $propertyName);
            }, $propertiesWhitelist);

            $columnTypes = $this->provider->getColumns($table, $columnWhitelist);

            $propertyTypes = [];
            foreach ($columnTypes as $columnName => $type) {
                $propertyName = $this->mapper->getEntityField($table, $columnName);
                $propertyTypes[$propertyName] = $type;
            }

            return $propertyTypes;
        });
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

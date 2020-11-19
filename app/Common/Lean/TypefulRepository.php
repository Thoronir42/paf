<?php declare(strict_types=1);

namespace PAF\Common\Lean;

use LeanMapper\Entity;
use Nette\InvalidStateException;
use SeStep\Typeful\Entity\EntityDescriptor;
use SeStep\Typeful\Service\EntityDescriptorRegistry;
use SeStep\Typeful\Service\TypeRegistry;
use SeStep\Typeful\Types\CommitAwareType;
use SeStep\Typeful\Types\HasDefaultValue;
use SeStep\Typeful\Types\OptionallyUpdate;
use SeStep\Typeful\Types\SerializesValue;

/**
 * @property-read string $typefulEntityName
 */
trait TypefulRepository
{
    /** @var EntityDescriptorRegistry */
    private $entityDescriptorRegistry;
    /** @var TypeRegistry */
    private $typeRegistry;

    public function injectTypefulRegistry(
        EntityDescriptorRegistry $entityDescriptorRegistry,
        TypeRegistry $typeRegistry
    ) {
        $this->entityDescriptorRegistry = $entityDescriptorRegistry;
        $this->typeRegistry = $typeRegistry;
    }

    public function createNewFromTypefulData(array $data): Entity
    {
        $descriptor = $this->getEntityDescriptor();

        $dataWithDefaults = $this->initDefaults($descriptor, $data);
        $normalizedData = $this->normalizeValuesBeforeSave($descriptor, $dataWithDefaults);
        $serializedData = $this->serializePropertyValues($descriptor, $normalizedData);

        $entity = $this->entityFactory->createEntity($this->getEntityClass(), $serializedData);
        $this->persist($entity);

        $this->commitValuesAfterSave($descriptor, $normalizedData);

        return $entity;
    }

    public function updateWIthTypefulData(Entity $entity, array $data)
    {
        $descriptor = $this->getEntityDescriptor();

        $currentRowData = $entity->getRowData();
        $updateData = $data + $currentRowData;
        $normalizedData = $this->normalizeValuesBeforeSave($descriptor, $updateData);
        $serializedData = $this->serializePropertyValues($descriptor, $normalizedData);
        unset($serializedData['id']);

        $entity->assign($serializedData);
        $this->persist($entity);

        $this->commitValuesAfterSave($descriptor, $normalizedData);

        return $entity;
    }

    private function getEntityDescriptor(): EntityDescriptor
    {
        if (!isset($this->typefulEntityName) || !$this->typefulEntityName) {
            $class = get_class($this);
            throw new InvalidStateException("Can't access entity descriptor on '$class'" .
                ", no 'typefulEntityName' set");
        }
        return $this->entityDescriptorRegistry->getEntityDescriptor($this->typefulEntityName, true);
    }

    private function initDefaults(EntityDescriptor $descriptor, array $data)
    {
        foreach ($descriptor->getProperties() as $name => $property) {
            $type = $this->typeRegistry->getType($property->getType());
            $options = $property->getOptions();
            $value = $data[$name] ?? null;
            if (!$value) {
                $value = $property->getDefaultValue($data);
            }
            if (!$value && $type instanceof HasDefaultValue) {
                $value = $type->getDefaultValue($data, $options);
            }

            $data[$name] = $value;
        }

        return $data;
    }

    private function normalizeValuesBeforeSave(EntityDescriptor $descriptor, array $data, $entity = null): array
    {
        foreach ($descriptor->getProperties() as $name => $property) {
            $type = $this->typeRegistry->getType($property->getType());
            $options = $property->getOptions();
            if (!isset($data[$name])) {
                continue;
            }

            if ($entity && $type instanceof OptionallyUpdate && !$type->shouldUpdate($data[$name], $options)) {
                unset($data[$name]);
                continue;
            }

            $value = $data[$name];
            $value = $property->normalizeValue($value);
            if ($type instanceof CommitAwareType) {
                $value = $type->normalizePreCommit($value, $options, $data);
            }

            $data[$name] = $value;
        }

        return $data;
    }

    private function serializePropertyValues(EntityDescriptor $descriptor, $data): array
    {
        $serialized = [];
        foreach ($descriptor->getProperties() as $name => $property) {
            if (!array_key_exists($name, $data)) {
                continue;
            }
            $value = $data[$name];
            $type = $this->typeRegistry->getType($property->getType());
            $propOptions = $property->getOptions();
            if ($type instanceof SerializesValue) {
                $value = $type->serialize($value, $propOptions);
            }
            $serialized[$name] = $value;
        }

        return $serialized;
    }

    private function commitValuesAfterSave(EntityDescriptor $descriptor, &$data)
    {
        foreach ($descriptor->getProperties() as $name => $property) {
            if (!array_key_exists($name, $data)) {
                continue;
            }
            $value = $data[$name];
            $type = $this->typeRegistry->getType($property->getType());
            $options = $property->getOptions();
            if ($type instanceof CommitAwareType) {
                $type->commitValue($value, $options);
            }
        }
    }
}

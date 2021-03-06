<?php declare(strict_types=1);

namespace SeStep\LeanFixtures;

use LeanMapper\Entity;
use LeanMapper\IMapper;
use LeanMapper\Reflection\Property;
use LeanMapper\Repository;

final class RepositoryFixtureDao implements FixtureDao
{
    private IMapper $mapper;
    private Repository $repository;

    private string $entityClass;

    public function __construct(BaseRepository $repository, IMapper $mapper)
    {
        $this->repository = $repository;
        $this->mapper = $mapper;

        $table = $this->mapper->getTableByRepositoryClass(get_class($this->repository));
        $this->entityClass = $this->mapper->getEntityClass($table);
    }

    public function create($entityData): int
    {
        $entityClass = $this->entityClass;
        $entity = new $entityClass();
        foreach ($entityData as $property => $value) {
            $entity->$property = $value;
        }

        if (!$this->repository->isPersistable($entity)) {
            return self::CREATE_NOT_UNIQUE;
        }

        $this->repository->persist($entity);

        return self::CREATE_OK;
    }

    public function findBy($value)
    {
        return $this->repository->find($value);
    }


    public function getPropertyRelatedClasses(): array
    {
        /** @var Entity|string $entityClass */
        $entityClass = $this->entityClass;
        $reflection = $entityClass::getReflection($this->mapper);

        $properties = $reflection->getEntityProperties();

        $modifiableRelatedProperties = array_filter($properties, function (Property $propertyReflection) {
            return $propertyReflection->isWritable() && $propertyReflection->hasRelationship();
        });

        return array_map(fn (Property $property) => $property->getType(), $modifiableRelatedProperties);
    }

    public function get($value)
    {
        return $this->repository->find($value);
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}

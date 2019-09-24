<?php declare(strict_types=1);

namespace PAF\Common\Fixtures;

use LeanMapper\Entity;
use LeanMapper\IMapper;
use LeanMapper\Reflection\Property;
use LeanMapper\Repository;
use PAF\Common\Model\BaseRepository;
use SeStep\LeanFixtures\FixtureDao;

final class RepositoryFixtureDao implements FixtureDao
{
    /** @var IMapper */
    private $mapper;
    /** @var Repository */
    private $repository;

    private $entityClass;

    public function __construct(BaseRepository $repository, IMapper $mapper)
    {
        $this->repository = $repository;
        $this->mapper = $mapper;
    }

    public function create($entityData): int
    {
        $entityClass = $this->getEntityClass();
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
        $entityClass = $this->getEntityClass();
        $reflection = $entityClass::getReflection($this->mapper);

        $properties = $reflection->getEntityProperties();

        $modifiableRelatedProperties = array_filter($properties, function (Property $propertyReflection) {
            return $propertyReflection->isWritable() && $propertyReflection->hasRelationship();
        });

        return array_map(function (Property $property) {
            return $property->getType();
        }, $modifiableRelatedProperties);
    }

    public function get($value)
    {
        return $this->repository->find($value);
    }

    public function getEntityClass(): string
    {
        if (!$this->entityClass) {
            $table = $this->mapper->getTableByRepositoryClass(get_class($this->repository));
            $this->entityClass = $this->mapper->getEntityClass($table);
        }

        return $this->entityClass;
    }
}

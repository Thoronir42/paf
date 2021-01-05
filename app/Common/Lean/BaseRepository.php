<?php declare(strict_types=1);

namespace PAF\Common\Lean;

use LeanMapper\Connection;
use LeanMapper\Entity;
use LeanMapper\IEntityFactory;
use LeanMapper\IMapper;
use LeanMapper\Repository;
use PAF\Common\Lean\RepositoryTraits;

abstract class BaseRepository extends Repository implements IQueryable
{
    use RepositoryTraits\HasIdGenerator;
    use RepositoryTraits\PersistencePrimitives;
    use RepositoryTraits\UniquenessCheck;

    protected LeanQueryFilter $queryFilter;

    public function __construct(
        Connection $connection,
        IMapper $mapper,
        IEntityFactory $entityFactory,
        LeanQueryFilter $queryFilter
    ) {
        parent::__construct($connection, $mapper, $entityFactory);
        $this->queryFilter = $queryFilter;
    }

    public function isPersistable(Entity $entity)
    {
        if ($entity->isDetached()) {
            return $this->isUnique($entity);
        }

        $entityClass = $this->mapper->getEntityClass($this->getTable());
        if (array_key_exists($this->mapper->getPrimaryKey($entityClass), $entity->getModifiedRowData())) {
            return $this->isUnique($entity);
        }

        return true;
    }

    final public function registerEvents(RepositoryEventsProvider $adapter)
    {
        foreach ($adapter->getEvents() as $type => $callback) {
            $this->events->registerCallback($type, $callback);
        }
    }

    ### Entity DAO code

    public function getEntityDataSource(array $conditions = []): LeanMapperDataSource
    {
        $entityClass = $this->mapper->getEntityClass($this->getTable());
        return new LeanMapperDataSource($this->select('t.*', 't', $conditions), $this, $this->mapper, $entityClass);
    }

    public function makeEntity($row)
    {
        if (!$row) {
            return null;
        }

        if (is_array($row)) {
            $class = $this->mapper->getEntityClass($this->getTable());
            $entity = new $class($row);
            $this->persist($entity);
            return $entity;
        }

        return $this->createEntity($row);
    }

    public function makeEntities(array $rows): array
    {
        return $this->createEntities($rows);
    }
}

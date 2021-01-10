<?php declare(strict_types=1);

namespace SeStep\LeanCommon\RepositoryTraits;

use LeanMapper\Fluent;
use LeanMapper\Entity;

trait PersistencePrimitives
{
    /**
     * @param $primaryKeyValue
     * @return Entity|null|Entity[]
     */
    public function find($primaryKeyValue)
    {
        $criteria = [$this->mapper->getPrimaryKey($this->getTable()) => $primaryKeyValue];

        if (is_array($primaryKeyValue)) {
            return $this->findBy($criteria);
        } else {
            return $this->findOneBy($criteria);
        }
    }

    public function findOneBy(array $criteria): ?Entity
    {
        $selection = $this->select('t.*', 't', $criteria);

        if ($finalRow = $selection->fetch()) {
            return $this->createEntity($finalRow);
        }

        return null;
    }

    /**
     * @param array $criteria
     * @param array $order
     * @param ?int $limit
     * @param ?int $offset
     *
     * @return Entity[]
     */
    public function findBy(array $criteria, $order = [], int $limit = null, int $offset = null): array
    {
        $selection = $this->select('t.*', 't', $criteria);

        if (is_integer($limit)) {
            $selection->limit($limit);
        }

        if (is_integer($offset)) {
            $selection->offset($offset);
        }

        if ($order) {
            $selection->orderBy($order);
        }

        return $this->createEntities($selection->fetchAll());
    }

    public function findAll(): array
    {
        $selection = $this->select();
        return $this->createEntities($selection->fetchAll());
    }

    public function countBy(array $criteria): int
    {
        $pk = $this->mapper->getPrimaryKey($this->getTable());

        $selection = $this->select("COUNT(t.$pk)", 't', $criteria);

        return $selection->fetchSingle();
    }

    public function deleteMany(array $entities): array
    {
        return array_map([$this, 'delete'], $entities);
    }


    protected function select(string $what = "t.*", string $alias = "t", array $criteria = null): Fluent
    {
        $fluent = $this->connection->select($what)
            ->from($this->getTable() . " AS $alias");

        if ($criteria) {
            $this->queryFilter->apply($fluent, $criteria, $this->mapper->getEntityClass($this->getTable()));
        }

        return $fluent;
    }

    /**
     * @param Entity $entity
     * @return mixed - id of last inserted entry
     */
    protected function insertIntoDatabase(Entity $entity)
    {
        $primaryKey = $this->mapper->getPrimaryKey($this->getTable());
        $values = $entity->getModifiedRowData();
        foreach ($values as &$value) {
            if ($value instanceof Entity) {
                $refPrimaryKey = $this->mapper->getPrimaryKey($this->mapper->getTable(get_class($value)));
                $value = $value->$refPrimaryKey;
            }
        }

        $this->connection->query('INSERT INTO %n %v', $this->getTable(), $values);

        return isset($values[$primaryKey]) ? $values[$primaryKey] : $this->connection->getInsertId();
    }
}

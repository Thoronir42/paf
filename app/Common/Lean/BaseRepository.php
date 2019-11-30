<?php declare(strict_types=1);

namespace PAF\Common\Lean;

use Dibi\Fluent;
use Dibi\UniqueConstraintViolationException;
use LeanMapper\Connection;
use LeanMapper\Entity;
use LeanMapper\IEntityFactory;
use LeanMapper\IMapper;
use LeanMapper\Repository;
use SeStep\EntityIds\IdGenerator;

abstract class BaseRepository extends Repository implements IQueryable
{
    private const MAX_ID_ATTEMPTS = 10;

    private static $CONDITIONS = [
        true => [
            'IN' => 'IN',
            'NULL' => 'IS NULL',
            'LIKE' => 'LIKE',
            'EQ' => '=',
        ],
        false => [
            'IN' => 'NOT IN',
            'NULL' => 'IS NOT NULL',
            'LIKE' => 'NOT LIKE',
            'EQ' => '!=',
        ],
    ];

    /** @var IdGenerator */
    protected $idGenerator;

    /** @var string */
    private $index;

    private $uniqueColumns = [];

    public function __construct(
        Connection $connection,
        IMapper $mapper,
        IEntityFactory $entityFactory,
        string $index = null
    ) {
        parent::__construct($connection, $mapper, $entityFactory);
        $this->index = $index;
        if ($index) {
            $this->uniqueColumns[] = $index;
        }
    }

    protected function initEvents()
    {
        $this->events->registerCallback($this->events::EVENT_BEFORE_CREATE, [$this, 'validateUnique']);
    }

    /**
     * Sets given idGenerator and initializes events
     *
     * @param IdGenerator $generator
     */
    public function bindIdGenerator(IdGenerator $generator)
    {
        if ($this->idGenerator) {
            throw new \RuntimeException("Id generator already set");
        }

        $this->idGenerator = $generator;

        $this->events->registerCallback($this->events::EVENT_BEFORE_CREATE, [$this, 'assignId']);
        $this->events->registerCallback($this->events::EVENT_BEFORE_UPDATE, [$this, 'validateAssignedId']);
    }


    protected function select(string $what = "t.*", string $alias = "t", array $criteria = null): Fluent
    {
        $fluent = $this->connection->select($what)
            ->from($this->getTable() . " AS $alias");

        if ($criteria) {
            $this->applyCriteria($fluent, $criteria);
        }

        return $fluent;
    }

    private function applyCriteria(Fluent $fluent, array &$criteria)
    {
        foreach ($criteria as $key => $value) {
            if ($key[0] === '!') {
                $key = substr($key, 1);
                $conditions = &self::$CONDITIONS[false];
            } else {
                $conditions = &self::$CONDITIONS[true];
            }

            if (is_array($value)) {
                $fluent->where("$key $conditions[IN] %in", $value);
            } elseif (is_null($value)) {
                $fluent->where("$key $conditions[NULL]");
            } elseif (is_string($value) && strpos($value, '%') !== false) {
                $fluent->where("$key $conditions[LIKE] ?", $value);
            } else {
                $fluent->where("$key $conditions[EQ] %s", $value);
            }
        }
    }

    public function find($primaryKeyValue)
    {
        $index = $this->index ?: $this->getPrimaryKey();
        $criteria = [$index => $primaryKeyValue];

        if (is_array($primaryKeyValue)) {
            return $this->findBy($criteria);
        } else {
            return $this->findOneBy($criteria);
        }
    }

    public function findOneBy(array $criteria)
    {
        $selection = $this->select('t.*', 't', $criteria);

        if ($finalRow = $selection->fetch()) {
            return $this->createEntity($finalRow);
        }

        return null;
    }

    public function findBy($criteria, $order = [], $limit = null, $offset = null)
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

    public function getDataSource(string $alias = null): Fluent
    {
        $select = $alias ? "$alias.*" : '*';

        return $this->select($select, $alias);
    }

    public function getEntityDataSource(): LeanMapperDataSource
    {
        $entityClass = $this->mapper->getEntityClass($this->getTable());

        $fluent = $this->connection->command();
        $fluent
            ->select('t.*')
            ->from($this->getTable() . ' AS t');
        
        return new LeanMapperDataSource($fluent, $this, $this->mapper, $entityClass);
    }

    public function makeEntity($row)
    {
        if (!$row) {
            return null;
        }

        return $this->createEntity($row);
    }

    public function makeEntities(array $rows): array
    {
        return $this->createEntities($rows);
    }

    protected function getPrimaryKey(): string
    {
        return $this->mapper->getPrimaryKey($this->getTable());
    }

    public function isPersistable(Entity $entity)
    {
        if ($entity->isDetached()) {
            return $this->isUnique($entity);
        }

        if (array_key_exists($this->getPrimaryKey(), $entity->getModifiedRowData())) {
            return $this->isUnique($entity);
        }

        return true;
    }


    protected function isUnique(Entity $entity)
    {
        if (empty($this->uniqueColumns)) {
            return true;
        }

        $primary = $this->getPrimaryKey();

        $orClauses = [];
        $orArgs = [];
        foreach ($this->uniqueColumns as $column) {
            if (!isset($entity->$column)) {
                continue;
            }
            $value = $entity->$column;
            if (is_null($value)) {
                continue;
            }

            $orClauses[] = "$column = ?";
            $orArgs[] = $value;
        }

        $check = $this->select("COUNT($primary)")
            ->where(implode(' OR ', $orClauses), $orArgs)
            ->fetchSingle();

        return $check === 0;
    }

    protected function insertIntoDatabase(Entity $entity)
    {
        $primaryKey = $this->getPrimaryKey();
        $values = $entity->getModifiedRowData();
        foreach ($values as &$value) {
            if ($value instanceof Entity) {
                $primaryKey = $this->mapper->getPrimaryKey($this->mapper->getTable(get_class($value)));
                $value = $value->$primaryKey;
            }
        }
        $this->connection->query(
            'INSERT INTO %n %v',
            $this->getTable(),
            $values
        );

        return isset($values[$primaryKey]) ? $values[$primaryKey] : $this->connection->getInsertId();
    }


    public function deleteMany(array $entities)
    {
        return array_map([$this, 'delete'], $entities);
    }

    public function validateUnique(Entity $entity)
    {
        if (!$this->isUnique($entity)) {
            throw new UniqueConstraintViolationException("Entity fails unique check");
        }
    }

    final public function registerEvents(RepositoryEventsProvider $adapter)
    {
        foreach ($adapter->getEvents() as $type => $callback) {
            $this->events->registerCallback($type, $callback);
        }
    }

    public function assignId(Entity $entity)
    {
        $type = get_class($entity);
        $primary = $this->mapper->getPrimaryKey($this->mapper->getTable($type));

        if (!isset($entity->$primary) || !$entity->$primary) {
            $entity->$primary = $this->getUniqueId($type);
        }
    }

    public function validateAssignedId(Entity $entity)
    {
        $type = get_class($entity);
        $primary = $this->mapper->getPrimaryKey($this->mapper->getTable($type));

        $changed = $entity->getModifiedRowData();
        if (!array_key_exists($primary, $changed)) {
            return;
        }
        if ($this->generator->getType($changed[$primary]) !== $type) {
            throw new \UnexpectedValueException("Id '{$changed[$primary]}' could not be validated for type '$type'");
        }
    }

    private function getUniqueId(string $type = null)
    {
        $i = 0;
        do {
            if (++$i > self::MAX_ID_ATTEMPTS) {
                throw new UniqueConstraintViolationException("Could not get an unique ID after "
                    . self::MAX_ID_ATTEMPTS . ' attempts');
            }
            $id = $this->idGenerator->generateId($type);
        } while ($this->find($id));

        return $id;
    }
}

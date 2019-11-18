<?php declare(strict_types=1);

namespace PAF\Common\Model;

use Dibi\Fluent;

abstract class BaseQueryObject
{
    /** @var IQueryable */
    protected $queryable;
    /** @var Fluent */
    protected $query;

    /**
     * BaseQueryObject constructor.
     *
     * @param IQueryable $queryable
     * @param string|null $tableAlias
     */
    public function __construct(IQueryable $queryable, string $tableAlias = null)
    {
        $this->query = $queryable->getDataSource($tableAlias);
    }

    /**
     * @param IQueryable $queryable
     */
    public function setQueryable(IQueryable $queryable): void
    {
        $this->queryable = $queryable;
    }


    public function limit(int $limit, int $offset = null)
    {
        $this->query->limit($limit);
        if ($offset !== null) {
            $this->query->offset($offset);
        }
    }

    public function fetch()
    {
        $this->queryable->makeEntity($this->query->fetch());
    }

    public function fetchAll()
    {
        return $this->queryable->makeEntities($this->query->fetchAll());
    }
}

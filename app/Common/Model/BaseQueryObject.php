<?php


namespace PAF\Common\Model;


use Dibi\DataSource;
use SeStep\Commentable\Lean\Model\Comment;

abstract class BaseQueryObject
{
    /** @var IQueryable */
    protected $queryable;
    /** @var DataSource */
    protected $dataSource;

    /**
     * BaseQueryObject constructor.
     * @param DataSource $dataSource
     */
    public function __construct(IQueryable $queryable, string $tableAlias = null)
    {
        $this->dataSource = $queryable->getDataSource($tableAlias);
    }

    public function limit(int $limit, int $offset = null)
    {
        $this->dataSource->applyLimit($limit, $offset);
    }

    public function fetch()
    {
        $this->queryable->makeEntity($this->dataSource->fetch());
    }

    public function fetchAll() {

        return $this->queryable->makeEntities($this->dataSource->fetchAll());
    }
}
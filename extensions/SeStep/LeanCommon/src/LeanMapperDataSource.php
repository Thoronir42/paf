<?php declare(strict_types=1);

namespace SeStep\LeanCommon;

use LeanMapper\Entity;
use LeanMapper\Fluent;
use LeanMapper\IMapper;
use Ublaboo\DataGrid\DataSource\FilterableDataSource;
use Ublaboo\DataGrid\DataSource\IDataSource;
use Ublaboo\DataGrid\Exception\DataGridDateTimeHelperException;
use Ublaboo\DataGrid\Filter;
use Ublaboo\DataGrid\Utils\DateTimeHelper;
use Ublaboo\DataGrid\Utils\Sorting;

/**
 * DataSource returning LeanMapper entities
 *
 * Carbon copy of {@link \Ublaboo\DataGrid\DataSource\DibiFluentDataSource} with addition of columns being mapped
 */
class LeanMapperDataSource extends FilterableDataSource implements IDataSource
{
    private Fluent $dataSource;
    private IQueryable $queryable;
    private IMapper $mapper;
    /** @var string|Entity */
    private string $entityClass;

    public function __construct(
        Fluent $dataSource,
        IQueryable $queryable,
        IMapper $mapper,
        string $entityClass
    ) {
        $this->dataSource = $dataSource;
        $this->queryable = $queryable;
        $this->mapper = $mapper;
        $this->entityClass = $entityClass;
    }

    public function getCount(): int
    {
        $table = $this->mapper->getTable($this->entityClass);
        $primary = $this->mapper->getPrimaryKey($table);
        $select = clone $this->dataSource;
        $select->removeClause('SELECT');
        $select->select("COUNT($primary)");

        return $select->fetchSingle();
    }

    public function getData(): array
    {
        return $this->queryable->makeEntities($this->dataSource->fetchAll());
    }

    /**
     * Filter data - get one row
     *
     * @param array $condition
     * @return static
     */
    public function filterOne(array $condition): IDataSource
    {
        $mappedCondition = [];
        foreach ($condition as $column => $value) {
            $mappedCondition[$this->getColumn($column)] = $value;
        }

        $this->dataSource->where($mappedCondition)->limit(1);

        return $this;
    }

    /**
     * Apply limit and offset on data
     *
     * @param int $offset
     * @param int $limit
     *
     * @return static
     */
    public function limit(int $offset, int $limit): IDataSource
    {
        $this->dataSource->offset($offset);
        $this->dataSource->limit($limit);

        return $this;
    }

    /**
     * Sort data
     *
     * @param Sorting $sorting
     * @return static
     */
    public function sort(Sorting $sorting): IDataSource
    {
        if (is_callable($sorting->getSortCallback())) {
            call_user_func(
                $sorting->getSortCallback(),
                $this->dataSource,
                $sorting->getSort()
            );

            return $this;
        }

        $sort = $sorting->getSort();

        if ($sort !== []) {
            $this->dataSource->removeClause('ORDER BY');
            $sortMapped = [];
            foreach ($sort as $column => $value) {
                $sortMapped[$this->getColumn($column)] = $value;
            }

            $this->dataSource->orderBy($sortMapped);
        }

        return $this;
    }

    protected function getDataSource(): Fluent
    {
        return $this->dataSource;
    }

    protected function applyFilterDate(Filter\FilterDate $filter): void
    {
        $conditions = $filter->getCondition();

        try {
            $date = DateTimeHelper::tryConvertToDateTime($conditions[$filter->getColumn()], [$filter->getPhpFormat()]);

            $column = $this->getPropertyColumn($filter);
            $this->dataSource->where('DATE(%n) = ?', $column, $date->format('Y-m-d'));
        } catch (DataGridDateTimeHelperException $ex) {
            trigger_error("Invalid date filter value");
        }
    }

    protected function applyFilterDateRange(Filter\FilterDateRange $filter): void
    {
        $conditions = $filter->getCondition();

        $valueFrom = $conditions[$filter->getColumn()]['from'];
        $valueTo = $conditions[$filter->getColumn()]['to'];

        $column = $this->getPropertyColumn($filter);

        if ($valueFrom) {
            try {
                $dateFrom = DateTimeHelper::tryConvertToDateTime($valueFrom, [$filter->getPhpFormat()]);
                $dateFrom->setTime(0, 0, 0);


                $this->dataSource->where('DATE(%n) >= ?', $column, $dateFrom);
            } catch (DataGridDateTimeHelperException $ex) {
                trigger_error("Invalid date range from filter value");
            }
        }

        if ($valueTo) {
            try {
                $dateTo = DateTimeHelper::tryConvertToDateTime($valueTo, [$filter->getPhpFormat()]);
                $dateTo->setTime(23, 59, 59);

                $this->dataSource->where('DATE(%n) <= ?', $column, $dateTo);
            } catch (DataGridDateTimeHelperException $ex) {
                trigger_error("Invalid date range to filter value");
            }
        }
    }

    protected function applyFilterRange(Filter\FilterRange $filter): void
    {
        $conditions = $filter->getCondition();

        $valueFrom = $conditions[$filter->getColumn()]['from'];
        $valueTo = $conditions[$filter->getColumn()]['to'];

        $column = $this->getPropertyColumn($filter);
        if ($valueFrom || $valueFrom !== '') {
            $this->dataSource->where('%n >= ?', $column, $valueFrom);
        }

        if ($valueTo || $valueTo !== '') {
            $this->dataSource->where('%n <= ?', $column, $valueTo);
        }
    }

    protected function applyFilterText(Filter\FilterText $filter): void
    {
        $condition = $filter->getCondition();
        $or = [];

        foreach ($condition as $column => $value) {
            $column = $this->getColumn($column);

            if ($filter->isExactSearch()) {
                $this->dataSource->where("$column = %s", $value);

                continue;
            }

            $words = $filter->hasSplitWordsSearch() === false ? [$value] : explode(' ', $value);

            foreach ($words as $word) {
                $or[] = ["$column LIKE %~like~", $word];
            }
        }

        if (sizeof($or) > 1) {
            $this->dataSource->where('(%or)', $or);
        } else {
            $this->dataSource->where($or);
        }
    }

    protected function applyFilterMultiSelect(Filter\FilterMultiSelect $filter): void
    {
        $condition = $filter->getCondition();
        $values = $condition[$filter->getColumn()];
        $column = $this->getPropertyColumn($filter);

        if (sizeof($values) > 1) {
            $value1 = array_shift($values);
            $length = sizeof($values);
            $i = 1;

            $this->dataSource->where('(%n = ?', $column, $value1);

            foreach ($values as $value) {
                if ($i === $length) {
                    $this->dataSource->__call('or', ['%n = ?)', $column, $value]);
                } else {
                    $this->dataSource->__call('or', ['%n = ?', $column, $value]);
                }

                $i++;
            }
        } else {
            $this->dataSource->where('%n = ?', $column, reset($values));
        }
    }

    protected function applyFilterSelect(Filter\FilterSelect $filter): void
    {
        $cond = [];
        foreach ($filter->getCondition() as $column => $value) {
            $cond[$this->getColumn($column)] = $value;
        }

        $this->dataSource->where($cond);
    }

    private function getPropertyColumn(Filter\OneColumnFilter $filter): string
    {
        return $this->getColumn($filter->getColumn());
    }

    private function getColumn(string $propertyName): string
    {
        $reflection = $this->entityClass::getReflection($this->mapper);
        return $reflection->getEntityProperty($propertyName)->getColumn();
    }
}

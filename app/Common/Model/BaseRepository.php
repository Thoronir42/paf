<?php declare(strict_types=1);

namespace PAF\Common\Model;


use Dibi\DataSource;
use Dibi\Fluent;
use LeanMapper\Repository;

class BaseRepository extends Repository implements IQueryable
{
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

    protected function select($what = "t.*", $alias = "t", array $criteria = null)
    {
        $fluent = $this->connection->command()->select($what)
            ->from($this->getTable() . " AS $alias");

        if ($criteria) {
            $this->applyCriteria($fluent, $criteria, $alias);
        }

        return $fluent;
    }

    private function applyCriteria(Fluent $fluent, array &$criteria, string $alias = null)
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

    public function getDataSource(string $alias = null): DataSource
    {
        $from = $this->getTable() . ($alias ? " AS $alias" : '');
        $select = $alias ? "$alias.*" : '*';

        return $this->connection->dataSource("SELECT $select FROM $from");
    }

    public function makeEntity($row)
    {
        if(!$row) {
            return null;
        }

        return $this->createEntity($row);
    }

    public function makeEntities(array $rows): array
    {
        return $this->createEntities($rows);
    }


}
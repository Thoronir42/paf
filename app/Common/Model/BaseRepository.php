<?php declare(strict_types=1);

namespace PAF\Common\Model;


use Dibi\Fluent;
use LeanMapper\Repository;
use PAF\Common\Model\Exceptions\EntityNotFoundException;

class BaseRepository extends Repository
{
    private static $CONDITIONS = [
        true => [
            'IN' => 'IN',
            'NULL' => 'IS NULL',
            '=' => '=',
        ],
        false => [
            'IN' => 'NOT IN',
            'NULL' => 'IS NOT NULL',
            '=' => '!=',
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
                $fluent->where($key . ' ' . $conditions['IN'] . '%in', $value);
            } elseif (is_null($value)) {
                $fluent->where($key . ' ' . $conditions['NULL']);
            } else {
                $fluent->where($key . ' ' . $conditions['='] . ' %s', $value);
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

    public function getDataSource()
    {
        return $this->connection->command()->from($this->getTable());
    }

}
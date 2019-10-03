<?php declare(strict_types=1);
namespace PAF\Common\Model;

use Dibi\Fluent;

interface IQueryable
{
    public function getDataSource(string $alias = null): Fluent;
    
    public function makeEntity($row);

    public function makeEntities(array $rows): array;
}

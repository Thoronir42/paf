<?php


namespace PAF\Common\Model;


use Dibi\DataSource;

interface IQueryable
{
    public function getDataSource(string $alias = null): DataSource;
    
    public function makeEntity($row);

    public function makeEntities(array $rows): array;
}
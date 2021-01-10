<?php declare(strict_types=1);

namespace SeStep\LeanCommon;

interface IQueryable
{
    public function makeEntity($row);

    public function makeEntities(array $rows): array;
}

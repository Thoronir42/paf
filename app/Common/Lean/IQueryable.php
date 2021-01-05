<?php declare(strict_types=1);

namespace PAF\Common\Lean;

interface IQueryable
{
    public function makeEntity($row);

    public function makeEntities(array $rows): array;
}

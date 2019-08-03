<?php declare(strict_types=1);

namespace SeStep\LeanFixtures\Loaders;

class StructuredFixtureGroup extends FixtureGroup
{
    private $dataIterator;

    public function __construct(string $entityClass, array $data)
    {
        parent::__construct($entityClass);
        $this->dataIterator = new \ArrayIterator($data);
    }

    public function entities(): \Iterator
    {
        return $this->dataIterator;
    }
}

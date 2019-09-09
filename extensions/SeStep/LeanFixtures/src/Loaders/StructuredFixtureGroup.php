<?php declare(strict_types=1);

namespace SeStep\LeanFixtures\Loaders;

class StructuredFixtureGroup extends FixtureGroup
{
    private $dataIterator;

    public function __construct(string $entityClass, string $name, array $data)
    {
        parent::__construct($entityClass, $name);
        $this->dataIterator = new \ArrayIterator($data);
    }

    public function entities(): \Iterator
    {
        return $this->dataIterator;
    }
}

<?php declare(strict_types=1);

namespace SeStep\LeanFixtures\Loaders;

use Iterator;

abstract class FixtureGroup
{
    protected string $entityClass;

    protected string $name;

    /**
     * @param string $entityClass
     * @param string $name
     */
    public function __construct(string $entityClass, string $name)
    {
        $this->entityClass = $entityClass;
        $this->name = $name;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function getName(): string
    {
        return $this->name;
    }

    abstract public function entities(): Iterator;
}

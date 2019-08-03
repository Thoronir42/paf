<?php declare(strict_types=1);

namespace SeStep\LeanFixtures\Loaders;

abstract class FixtureGroup
{
    /** @var string */
    protected $entityClass;

    /**
     * EntityGroup constructor.
     * @param string $entityClass
     */
    public function __construct(string $entityClass)
    {
        $this->entityClass = $entityClass;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    abstract public function entities(): \Iterator;
}

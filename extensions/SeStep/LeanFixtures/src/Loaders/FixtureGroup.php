<?php declare(strict_types=1);

namespace SeStep\LeanFixtures\Loaders;

abstract class FixtureGroup
{
    /** @var string */
    protected $entityClass;

    /** @var string */
    protected $name;

    /**
     * EntityGroup constructor.
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

    abstract public function entities(): \Iterator;
}

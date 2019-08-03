<?php declare(strict_types=1);

namespace SeStep\LeanFixtures;

interface FixtureDao
{

    public function getEntityClass(): string;

    public function create($entityData);

    public function findBy($value);

    /**
     * Returns associative array of
     *  - property => classname
     *
     * @return array
     */
    public function getPropertyRelatedClasses(): array;
}

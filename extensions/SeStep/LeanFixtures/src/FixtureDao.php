<?php declare(strict_types=1);

namespace SeStep\LeanFixtures;

interface FixtureDao
{

    const CREATE_OK = 0;
    const CREATE_NOT_UNIQUE = -1;

    public function getEntityClass(): string;

    public function create($entityData): int;

    public function findBy($value);

    /**
     * Returns associative array of
     *  - property => classname
     *
     * @return array
     */
    public function getPropertyRelatedClasses(): array;
}

<?php declare(strict_types=1);

namespace SeStep\LeanFixtures\Loaders;

interface FixtureLoader
{
    /**
     * @return FixtureGroup[]|\Traversable
     */
    public function getGroups();

    /**
     * @return string - identifying name of the loader
     */
    public function getName(): string;
}

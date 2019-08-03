<?php declare(strict_types=1);

namespace SeStep\LeanFixtures;

use Nette\InvalidStateException;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

final class EntityFixtures
{
    /** @var FixtureDao[] */
    private $daoByClass = [];

    public function addFixtureDao(FixtureDao $dao)
    {
        $class = $dao->getEntityClass();
        if (isset($this->daoByClass[$class])) {
            throw new InvalidStateException("FixtureLoader for class '$class' already registered");
        }

        $this->daoByClass[$class] = $dao;
    }

    public function loadData(Loaders\FixtureLoader $loader, OutputInterface $output = null)
    {
        if (!$output) {
            $output = new NullOutput();
        }

        $output->writeln("Loading from " . $loader->getName());
        foreach ($loader->getGroups() as $name => $group) {
            try {
                $output->writeln("- processing group '$name'", OutputInterface::VERBOSITY_VERBOSE);
                $count = $this->loadGroup($group, $output);
                $output->writeln("- loaded $count items from group '$name'");
            } catch (\Throwable $ex) {
                $output->writeln("Failed to process group '$name': " . $ex->getMessage());
            }
        }
    }

    private function loadGroup(Loaders\FixtureGroup $group, OutputInterface $output): int
    {
        $class = $group->getEntityClass();
        $dao = $this->getDaoByEntityClass($class);

        $propertyClasses = $dao->getPropertyRelatedClasses();

        $i = 0;
        foreach ($group->entities() as $n => $entityData) {
            try {
                $dao->create($entityData);
                $i++;
            } catch (\Throwable $ex) {
                $output->writeln("Failed to load item $n: " . $ex->getMessage());
            }
        }

        return $i;
    }

    private function getDaoByEntityClass(string $class): FixtureDao
    {
        if (!isset($this->daoByClass[$class])) {
            throw new InvalidStateException("Entity '$class' currently does not have a dao configured");
        }

        return $this->daoByClass[$class];
    }
}

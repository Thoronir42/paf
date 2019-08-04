<?php declare(strict_types=1);

namespace SeStep\LeanFixtures;

use Nette\InvalidStateException;
use Nette\UnexpectedValueException;
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
            if (!is_array($entityData) || (is_object($entityData) && !$entityData instanceof \ArrayAccess)) {
                $output->writeln("Error: Group value '$n' is not an associative array");
                continue;
            }

            try {
                foreach ($entityData as $property => $value) {
                    if (array_key_exists($property, $propertyClasses)) {
                        $entityData[$property] = $this->findEntityByValue($propertyClasses[$property], $value,
                            $property);
                    }
                }

                $dao->create($entityData);
                $i++;
            } catch (\Throwable $ex) {
                $output->writeln("Failed to load item $n: " . $ex->getMessage());
                $output->writeln($ex->getTraceAsString(), OutputInterface::VERBOSITY_VERY_VERBOSE);
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

    private function findEntityByValue(string $class, $value, string $property)
    {
        $propertyDao = $this->getDaoByEntityClass($class);
        $relatedEntity = $propertyDao->findBy($value);
        if (!$relatedEntity instanceof $class) {
            $type = is_object($relatedEntity) ? get_class($relatedEntity) : gettype($relatedEntity);
            $msg = "Related value of property '$property' expected to be instance of $class, got: $type";
            throw new UnexpectedValueException($msg);
        }

        return $relatedEntity;
    }
}

<?php declare(strict_types=1);

namespace SeStep\LeanFixtures;

use ArrayAccess;
use Nette\InvalidStateException;
use Nette\UnexpectedValueException;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

final class EntityFixtures
{
    /** @var FixtureDao[] */
    private array $daoByClass;


    /**
     * @param FixtureDao[] $daos
     */
    public function __construct(array $daos)
    {
        $this->daoByClass = self::initializeDaoStructure($daos);
    }

    public function loadData(Loaders\FixtureLoader $loader, OutputInterface $output = null)
    {
        if (!$output) {
            $output = new NullOutput();
        }

        $output->writeln("Loading from " . $loader->getName());
        foreach ($loader->getGroups() as $group) {
            $name = $group->getName();
            try {
                $output->writeln("- processing group '$name'", OutputInterface::VERBOSITY_VERBOSE);
                $count = $this->loadGroup($group, $output);
                $output->writeln("- loaded $count items from group '$name'");
            } catch (Throwable $ex) {
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
            $fixtureId = $group->getName() . "[$n]";
            if (!is_array($entityData) || (is_object($entityData) && !$entityData instanceof ArrayAccess)) {
                $output->writeln("Error: Fixture '$fixtureId' is not an associative array");
                continue;
            }

            try {
                foreach ($entityData as $property => $value) {
                    if (array_key_exists($property, $propertyClasses)) {
                        $entityData[$property] = $this->findEntityByValue(
                            $propertyClasses[$property],
                            $value,
                            $property
                        );
                    }
                }

                $result = $dao->create($entityData);

                switch ($result) {
                    case $dao::CREATE_NOT_UNIQUE:
                        $output->writeln("Fixture $fixtureId already exists", $output::VERBOSITY_VERBOSE);
                        break;
                }
                if ($result >= 0) {
                    $i++;
                }
            } catch (Throwable $ex) {
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

    /**
     * Validates array items to be FixtureDao instances, puts them into map
     * by key which equals to their corresponding entity.
     *
     * @param FixtureDao[] $daos
     *
     * @return FixtureDao[] resulting map
     */
    private static function initializeDaoStructure(array $daos): array
    {
        $daoByEntityClass = [];
        foreach ($daos as $key => $dao) {
            if (!$dao instanceof FixtureDao) {
                throw new \UnexpectedValueException("Item $key expected to be instance of "
                    . FixtureDao::class . ', got ' . get_class($dao));
            }

            $class = $dao->getEntityClass();
            if (isset($daoByEntityClass[$class])) {
                throw new InvalidStateException("FixtureLoader for class '$class' already registered");
            }

            $daoByEntityClass[$class] = $dao;
        }

        return $daoByEntityClass;
    }
}

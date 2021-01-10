<?php declare(strict_types=1);

namespace SeStep\LeanCommon\RepositoryTraits;

use Dibi\UniqueConstraintViolationException;
use LeanMapper\Entity;
use RuntimeException;
use SeStep\EntityIds\IdGenerator;
use UnexpectedValueException;

trait HasIdGenerator
{
    protected IdGenerator $idGenerator;

    protected int $maxIdAttempts = 10;

    /**
     * Sets given idGenerator and initializes events
     *
     * @param IdGenerator $generator
     */
    public function bindIdGenerator(IdGenerator $generator): void
    {
        if (isset($this->idGenerator)) {
            throw new RuntimeException("Id generator already set");
        }

        $this->idGenerator = $generator;

        $this->events->registerCallback($this->events::EVENT_BEFORE_CREATE, [$this, 'assignId']);
        $this->events->registerCallback($this->events::EVENT_BEFORE_UPDATE, [$this, 'validateAssignedId']);
    }

    public function assignId(Entity $entity): void
    {
        $type = get_class($entity);
        $primary = $this->mapper->getPrimaryKey($this->mapper->getTable($type));

        if (!isset($entity->$primary) || !$entity->$primary) {
            $entity->$primary = $this->getUniqueId($type);
        }
    }

    /**
     * @param Entity $entity
     */
    public function validateAssignedId(Entity $entity): void
    {
        $type = get_class($entity);
        $primary = $this->mapper->getPrimaryKey($this->mapper->getTable($type));

        $changed = $entity->getModifiedRowData();
        if (!array_key_exists($primary, $changed)) {
            return;
        }
        if ($this->idGenerator->getType($changed[$primary]) !== $type) {
            throw new UnexpectedValueException("Id '{$changed[$primary]}' could not be validated for type '$type'");
        }
    }

    private function getUniqueId(string $type): string
    {
        for ($i = 0; $i < $this->maxIdAttempts; $i++) {
            $id = $this->idGenerator->generateId($type);
            if (!$this->find($id)) {
                return $id;
            }
        }

        throw new UniqueConstraintViolationException("Could not get an unique ID after "
            . $this->maxIdAttempts . ' attempts');
    }
}

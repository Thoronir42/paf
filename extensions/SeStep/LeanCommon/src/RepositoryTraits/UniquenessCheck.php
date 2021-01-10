<?php declare(strict_types=1);

namespace SeStep\LeanCommon\RepositoryTraits;

use Dibi\UniqueConstraintViolationException;
use LeanMapper\Entity;

trait UniquenessCheck
{
    /** @var string[] */
    protected array $uniqueProperties = [];

    public function ensureUnique(string ...$uniqueProperties): void
    {
        foreach ($uniqueProperties as $property) {
            $this->uniqueProperties[] = $property;
        }
    }

    public function registerUniquenessEvents(): void
    {
        $this->events->registerCallback($this->events::EVENT_BEFORE_CREATE, [$this, 'validateUnique']);
    }

    /**
     * @param Entity $entity
     * @throws UniqueConstraintViolationException
     */
    public function validateUnique(Entity $entity): void
    {
        if (!$this->isUnique($entity)) {
            throw new UniqueConstraintViolationException("Entity fails unique check");
        }
    }

    protected function isUnique(Entity $entity): bool
    {
        $columns = $this->uniqueProperties;

        $primary = $this->mapper->getPrimaryKey($this->getTable());
        if (!in_array($primary, $columns)) {
            $columns[] = $primary;
        }

        $orClauses = [];
        $orArgs = [];
        foreach ($columns as $column) {
            if (!isset($entity->$column)) {
                continue;
            }
            $value = $entity->$column;
            if (is_null($value)) {
                continue;
            }

            $orClauses[] = "$column = ?";
            $orArgs[] = $value;
        }

        if (empty($orClauses)) {
            return true;
        }

        $check = $this->select("COUNT($primary)")
            ->where(implode(' OR ', $orClauses), $orArgs)
            ->fetchSingle();

        return $check === 0;
    }
}

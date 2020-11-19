<?php declare(strict_types=1);

namespace PAF\Common\Lean;

use LeanMapper\DefaultEntityFactory;
use LeanMapper\IEntityFactory;

class DummyTypefulRepository
{
    use TypefulRepository;

    /** @var IEntityFactory */
    private $entityFactory;

    private $persisted = [];

    public function __construct()
    {
        $this->typefulEntityName = 'common.testDummy';
        $this->entityFactory = new DefaultEntityFactory();
    }

    public function getPersistedEntities(): array
    {
        return $this->persisted;
    }

    protected function persist($value)
    {
        $this->persisted[] = $value;
    }

    protected function getEntityClass()
    {
        return DummyEntity::class;
    }
}

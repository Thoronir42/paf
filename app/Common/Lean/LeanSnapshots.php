<?php declare(strict_types=1);

namespace PAF\Common\Lean;

use LeanMapper\Entity;
use Nette\Caching\Cache;
use Nette\Caching\Storages\MemoryStorage;

class LeanSnapshots
{
    /** @var Cache */
    private $snaps;

    public function __construct()
    {
        $this->snaps = new Cache(new MemoryStorage());
    }

    public function store(Entity $entity)
    {
        $this->snaps->save(spl_object_hash($entity), $this->serialize($entity));
    }

    public function retrieve(Entity $entity): ?array
    {
        return $this->snaps->load(spl_object_hash($entity));
    }

    public function compare(Entity $entity): ?array
    {
        $stored = $this->retrieve($entity);

        if ($stored === null) {
            return null;
        }

        return array_diff($stored, $this->serialize($entity));
    }

    private function serialize(Entity $entity): array
    {
        $data = $entity->getRowData();
        foreach ($data as &$value) {
            if ($value instanceof \DateTime) {
                $value = $value->format(\DateTime::W3C);
            }
        }

        return $data;
    }
}

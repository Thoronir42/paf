<?php declare(strict_types=1);

namespace PAF\Common\Lean;

use SeStep\Typeful\Entity\EntityDescriptor;
use SeStep\Typeful\Service\TypefulRepository;
use SeStep\Typeful\Service\TypeRegistry;

class LeanTypefulRepository extends TypefulRepository
{
    private BaseRepository $repository;

    public function __construct(
        BaseRepository $repository,
        EntityDescriptor $entityDescriptor,
        TypeRegistry $typeRegistry
    ) {
        parent::__construct($entityDescriptor, $typeRegistry);
        $this->repository = $repository;
    }

    protected function createFromData(array $data)
    {
        return $this->repository->makeEntity($data);
    }

    protected function updateByData($entity, $data): bool
    {
        $entity->assign($data);
        return !!$this->repository->persist($entity);
    }

    public function getRepository(): BaseRepository
    {
        return $this->repository;
    }
}

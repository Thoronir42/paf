<?php declare(strict_types=1);

namespace PAF\Common\AuditTrail\Facade;

use LeanMapper\Entity;
use LeanMapper\IMapper;
use PAF\Common\Lean\LeanSnapshots;
use PAF\Common\Lean\RepositoryEventsProvider;

abstract class AuditTrailRepositoryAdapter implements RepositoryEventsProvider
{
    protected AuditTrailService $auditTrailService;
    protected LeanSnapshots $snapshots;
    protected IMapper $mapper;

    public function __construct(AuditTrailService $auditTrailService, LeanSnapshots $snapshots, IMapper $mapper)
    {
        $this->auditTrailService = $auditTrailService;
        $this->snapshots = $snapshots;
        $this->mapper = $mapper;
    }

    protected function compare(Entity $entity)
    {
        // TODO: Investigate whether entity reflection shouldn't be used
        $tableName = $this->mapper->getTable(get_class($entity));

        $diff = $this->snapshots->compare($entity);
        if ($diff) {
            $changed = [];
            foreach ($diff as $prop => $oldValue) {
                $propertyName = $this->mapper->getEntityField($tableName, $prop);
                $changed[$propertyName] = [
                    'prop' => $propertyName,
                    'newValue' => $entity->$propertyName,
                    'oldValue' => $oldValue,
                ];
            }
        } else {
            $changed = [];
            foreach ($entity->getModifiedRowData() as $prop => $oldValue) {
                $propertyName = $this->mapper->getEntityField($tableName, $prop);
                $changed[$propertyName] = [
                    'prop' => $propertyName,
                    'newValue' => $entity->$propertyName,
                ];
            }
        }

        return $this->auditTrailService->normalizeValues($changed);
    }
}

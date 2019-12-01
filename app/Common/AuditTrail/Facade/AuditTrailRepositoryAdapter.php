<?php declare(strict_types=1);

namespace PAF\Common\AuditTrail\Facade;

use LeanMapper\Entity;
use LeanMapper\IMapper;
use PAF\Common\Lean\LeanSnapshots;
use PAF\Common\Lean\RepositoryEventsProvider;

abstract class AuditTrailRepositoryAdapter implements RepositoryEventsProvider
{
    /** @var AuditTrailService */
    protected $auditTrailService;
    /** @var LeanSnapshots */
    protected $snapshots;
    /** @var IMapper */
    protected $mapper;

    public function __construct(AuditTrailService $auditTrailService, LeanSnapshots $snapshots, IMapper $mapper)
    {
        $this->auditTrailService = $auditTrailService;
        $this->snapshots = $snapshots;
        $this->mapper = $mapper;
    }

    protected function compare(Entity $entity)
    {
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

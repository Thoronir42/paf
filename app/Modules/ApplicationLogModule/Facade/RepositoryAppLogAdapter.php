<?php declare(strict_types=1);

namespace PAF\Modules\ApplicationLogModule\Facade;

use LeanMapper\Entity;
use LeanMapper\IMapper;
use PAF\Common\Model\LeanSnapshots;
use PAF\Modules\CommissionModule\Model\PafCase;

abstract class RepositoryAppLogAdapter
{
    /** @var AppLog */
    protected $appLog;
    /** @var LeanSnapshots */
    protected $snapshots;
    /** @var IMapper */
    protected $mapper;

    public function __construct(AppLog $appLog, LeanSnapshots $snapshots, IMapper $mapper)
    {
        $this->appLog = $appLog;
        $this->snapshots = $snapshots;
        $this->mapper = $mapper;
    }

    /**
     * @return callable[] Array of callbacks with keys being event types of {@link \LeanMapper\Events}
     */
    abstract public function getEvents(): array;

    protected function compare(Entity $entity)
    {
        $tableName = $this->mapper->getTable(get_class($entity));

        $diff = $this->snapshots->compare($entity);
        if ($diff) {
            $changed = [];
            foreach ($diff as $prop => $oldValue) {
                $propertyName = $this->mapper->getEntityField($tableName, $prop);
                $changed[$prop] = [
                    'prop' => $prop,
                    'newValue' => $entity->$propertyName,
                    'oldValue' => $oldValue,
                ];
            }
        } else {
            $changed = [];
            foreach ($entity->getModifiedRowData() as $prop => $oldValue) {
                $propertyName = $this->mapper->getEntityField($tableName, $prop);
                $changed[$prop] = [
                    'prop' => $prop,
                    'newValue' => $entity->$propertyName,
                ];
            }
        }

        return $this->appLog->normalizeValues($changed);
    }
}

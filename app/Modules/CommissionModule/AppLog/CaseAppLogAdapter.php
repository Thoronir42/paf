<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\AppLog;

use LeanMapper\Events;
use PAF\Modules\ApplicationLogModule\Facade\RepositoryAppLogAdapter;
use PAF\Modules\CommissionModule\Model\PafCase;

class CaseAppLogAdapter extends RepositoryAppLogAdapter
{

    /**
     * @return array
     */
    public function getEvents(): array
    {
        return [
            Events::EVENT_AFTER_CREATE => [$this, 'afterCreate'],
            Events::EVENT_AFTER_UPDATE => [$this, 'afterUpdate'],
        ];
    }

    public function afterCreate(PafCase $case)
    {
        $this->appLog->addEvent($case->id, 'commission.log.caseCreated');
    }

    public function afterUpdate(PafCase $case)
    {
        $this->appLog->addEvent($case->id, 'commission.log.caseUpdated', [
            'properties' => array_keys($case->getModifiedRowData()),
        ]);
    }
}

<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\AuditTrail;

use LeanMapper\Events;
use PAF\Modules\AuditTrailModule\Facade\AuditTrailRepositoryAdapter;
use PAF\Modules\CommissionModule\Model\PafCase;

class CaseAuditTrailAdapter extends AuditTrailRepositoryAdapter
{

    /**
     * @inheritDoc
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
        $this->auditTrailService->addEvent($case->id, 'commission.log.caseCreated');
    }

    public function afterUpdate(PafCase $case)
    {
        $parameters['changes'] = $this->compare($case);

        $this->auditTrailService->addEvent($case->id, 'commission.log.caseUpdated', $parameters);
    }
}

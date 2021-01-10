<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\AuditTrail;

use LeanMapper\Events;
use SeStep\NetteAuditTrail\Facade\AuditTrailRepositoryAdapter;
use PAF\Modules\CommissionModule\Model\Commission;

class CommissionAuditTrailAdapter extends AuditTrailRepositoryAdapter
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

    public function afterCreate(Commission $commission)
    {
        $this->auditTrailService->addEvent($commission->id, 'commission.log.commissionCreated');
    }

    public function afterUpdate(Commission $commission)
    {
        $parameters['changes'] = $this->compare($commission);

        $this->auditTrailService->addEvent($commission->id, 'commission.log.commissionUpdated', $parameters);
    }
}

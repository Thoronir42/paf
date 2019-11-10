<?php declare(strict_types=1);

namespace PAF\Modules\AuditTrailModule\Repository;

use PAF\Common\Model\BaseRepository;

class EntryRepository extends BaseRepository
{
    public function getEventFeedQuery(string $subject)
    {
        return $this->select('te.id, te.instant', 'te')
            ->where('subject = ?', $subject);
    }
}

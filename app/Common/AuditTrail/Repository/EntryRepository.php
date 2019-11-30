<?php declare(strict_types=1);

namespace PAF\Common\AuditTrail\Repository;

use PAF\Common\Lean\BaseRepository;

class EntryRepository extends BaseRepository
{
    public function getEventFeedQuery(string $subject)
    {
        return $this->select('te.id, te.instant', 'te')
            ->where('subject = ?', $subject);
    }
}

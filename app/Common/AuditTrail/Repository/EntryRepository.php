<?php declare(strict_types=1);

namespace PAF\Common\AuditTrail\Repository;

use PAF\Common\Feed\Source\FeedSource;
use PAF\Common\Lean\BaseRepository;
use PAF\Common\Lean\LeanRepositoryFeedSource;

class EntryRepository extends BaseRepository
{
    public function getFeedSource(string $subject): FeedSource
    {
        $select = $this->select('te.id, te.instant', 'te')
            ->where('subject = ?', $subject);

        return new LeanRepositoryFeedSource($this, $select);
    }
}

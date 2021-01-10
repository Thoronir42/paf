<?php declare(strict_types=1);

namespace SeStep\NetteAuditTrail\Repository;

use SeStep\NetteFeed\Source\FeedSource;
use SeStep\LeanCommon\BaseRepository;
use SeStep\LeanCommon\LeanRepositoryFeedSource;

class EntryRepository extends BaseRepository
{
    public function getFeedSource(string $subject): FeedSource
    {
        $select = $this->select('te.id, te.instant', 'te')
            ->where('subject = ?', $subject);

        return new LeanRepositoryFeedSource($this, $select);
    }
}

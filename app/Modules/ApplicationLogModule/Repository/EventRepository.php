<?php


namespace PAF\Modules\ApplicationLogModule\Repository;

use PAF\Common\Model\BaseRepository;

class EventRepository extends BaseRepository
{

    public function getEventFeedQuery(string $subject)
    {
        return $this->select('le.id, le.instant', 'le')
            ->where('subject = ?', $subject);
    }
}

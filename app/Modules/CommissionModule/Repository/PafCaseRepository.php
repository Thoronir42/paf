<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Repository;

use PAF\Common\Model\BaseRepository;
use PAF\Modules\CommissionModule\Model\PafCase;

class PafCaseRepository extends BaseRepository
{
    public function getCasesByStatus($status = null)
    {
        if (!$status) {
            $status = [PafCase::STATUS_ACCEPTED, PafCase::STATUS_WIP];
        }
        if (is_string($status)) {
            $status = [$status];
        }

        $query = $this->select('c.*', 'c', [
            'c.status' => $status
        ]);
        $query->orderBy('c.accepted_on');

        return $this->createEntities($query->fetchAll());
    }
}

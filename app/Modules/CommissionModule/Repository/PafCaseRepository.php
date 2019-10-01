<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Repository;

use PAF\Common\Model\BaseRepository;
use PAF\Modules\CommissionModule\Model\PafCase;

// todo: reimplement
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

    /**
     * @param string $name
     * @param bool $deleted
     * @return PafCase?
     */
    public function getByName($name, $deleted = false)
    {
        $qb = $this->repository->createQueryBuilder('c');
        $expr = $qb->expr();

        $qb->join('c.wrapper', 'pw');

        $qb->where($expr->andX($expr->eq('pw.name', ':name'), $expr->eq('pw.deleted', ':deleted')));

        return $qb->getQuery()
            ->setParameters([
                'name' => $name,
                'deleted' => $deleted,
            ])
            ->getOneOrNullResult();
    }
}

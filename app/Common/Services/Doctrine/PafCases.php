<?php

namespace App\Common\Services\Doctrine;


use App\Common\Model\Entity\PafCase;
use SeStep\Model\BaseDoctrineService;

class PafCases extends BaseDoctrineService
{
    use SlugService;

    public function getCasesByStatus($status = null)
    {
        if (!$status) {
            $status = [PafCase::STATUS_ACCEPTED, PafCase::STATUS_WIP];
        }
        if (is_string($status)) {
            $status = [$status];
        }

        $qb = $this->repository->createQueryBuilder('q');
        $qb->join('q.wrapper', 'pw')->addSelect('pw');
        $qb->where("q.status IN (:status)");

        /** @var PafCase[] $result */
        $result = $qb->getQuery()
            ->execute([
                'status' => $status,
            ]);

        $quotes = [];

        foreach ($result as $case) {
            $quotes[$case->getFeName()] = $case;
        }

        return $quotes;
    }

    /**
     * @param string $name
     * @param bool   $deleted
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
                'name'    => $name,
                'deleted' => $deleted,
            ])
            ->getOneOrNullResult();

    }
}

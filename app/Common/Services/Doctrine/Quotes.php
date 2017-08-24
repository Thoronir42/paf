<?php

namespace App\Common\Services\Doctrine;

use App\Common\Model\Entity\Quote;
use SeStep\Model\BaseDoctrineService;

class Quotes extends BaseDoctrineService
{

    use SlugService;

    public function findForOverview($limit = 10, $page = 1)
    {
        $offset = ($page - 1) * $limit;

        $qb = $this->repository->createQueryBuilder('q');
        $qb
            ->where("q.status = :status")
            ->andWhere("length(q.slug) > 0");
        $result = $qb->getQuery()
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->execute([
                'status' => Quote::STATUS_NEW
            ]);

        $quotes = [];

        foreach ($result as $quote) {
            $quotes[$quote->getSlug()] = $quote;
        }

        return $quotes;
    }

    public function saveNew(Quote $quote)
    {
        if ($this->slugExists($quote->getSlug())) {
            return false;
        }

        $this->save($quote);
        return true;
    }
}

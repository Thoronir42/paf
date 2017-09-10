<?php

namespace App\Common\Services\Doctrine;

use App\Common\Model\Entity\PafWrapper;
use App\Common\Model\Entity\Quote;
use Doctrine\ORM\Query\Lexer;
use SeStep\Model\BaseDoctrineService;

class Quotes extends BaseDoctrineService
{

    use SlugService;

    public function findForOverview($limit = 10, $page = 1)
    {
        $offset = ($page - 1) * $limit;

        $qb = $this->repository->createQueryBuilder('q');
        $qb
            ->where("q.status = :status");
        $qb
            ->join(PafWrapper::class, 'pw', 'WHERE', 'q.id = pw.quote');
        /** @var Quote[] $result */
        $result = $qb->getQuery()
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->execute([
                'status' => Quote::STATUS_NEW,
            ]);

        $quotes = [];

        foreach ($result as $quote) {
            $quotes[$quote->getFeName()] = $quote;
        }

        return $quotes;
    }
}

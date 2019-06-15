<?php declare(strict_types=1);

namespace PAF\Modules\QuoteModule\Repository;


use PAF\Common\Model\BaseRepository;

class QuoteRepository extends BaseRepository
{
    public function findForOverview($limit = 10, $page = 1)
    {
        $offset = ($page - 1) * $limit;

        $qb = $this->repository->createQueryBuilder('q');
        $qb->join('q.wrapper', 'pw')->addSelect('pw');
        $qb->where("q.status = :status");

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

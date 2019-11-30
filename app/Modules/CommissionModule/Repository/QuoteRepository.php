<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Repository;

use Nette\Utils\Paginator;
use PAF\Common\Lean\BaseRepository;
use PAF\Modules\CommissionModule\Model\Quote;

class QuoteRepository extends BaseRepository
{
    public function findForOverview(Paginator $paginator = null)
    {
        $select = $this->select('q.*', 'q')
            ->where('q.status = ?', Quote::STATUS_NEW);

        if ($paginator) {
            $select->offset($paginator->getOffset())->limit($paginator->getItemsPerPage());
        }

        /** @var Quote[] $result */
        $result = $select->fetchAssoc('slug');

        return $this->createEntities($result);
    }
}

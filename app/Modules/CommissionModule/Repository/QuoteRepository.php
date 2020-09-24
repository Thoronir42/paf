<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Repository;

use Nette\Utils\Paginator;
use PAF\Common\Lean\BaseRepository;
use PAF\Modules\CommissionModule\Model\Quote;
use PAF\Modules\DirectoryModule\Model\Person;

class QuoteRepository extends BaseRepository
{
    public function findForOverview(Person $supplier = null, Paginator $paginator = null)
    {
        $select = $this->select('q.*', 'q', [
            'status' => Quote::STATUS_NEW,
            'supplier' => $supplier,
        ]);

        if ($paginator) {
            $select->offset($paginator->getOffset())->limit($paginator->getItemsPerPage());
        }

        /** @var Quote[] $result */
        $result = $select->fetchAssoc('slug');

        return $this->createEntities($result);
    }
}

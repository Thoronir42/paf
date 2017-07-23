<?php

namespace App\Common\Services\Doctrine;

use App\Common\Model\Entity\Quote;
use SeStep\Model\BaseDoctrineService;

class Quotes extends BaseDoctrineService
{

    use SlugService;

    public function findForOverview()
    {
        return $this->repository->findBy(['status' => 'new'], ['dateCreated' => 'ASC']);
    }
}

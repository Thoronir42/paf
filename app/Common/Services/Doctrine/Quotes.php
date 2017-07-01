<?php

namespace App\Common\Services\Doctrine;

use App\Common\Model\Entity\Quote;
use SeStep\Model\BaseDoctrineService;

class Quotes extends BaseDoctrineService
{
    protected $entity_class = Quote::class;

    public function findForOverview()
    {
        $picked = $this->repository->findBy(['status' => 'selected']);
        $new = $this->repository->findBy(['status' => 'new']);

        return [
            'picked' => $picked,
            'new' => $new,
        ];
    }
}

<?php

namespace App\Common\Services\Doctrine;


use App\Common\Model\Entity\PafCase;
use SeStep\Model\BaseDoctrineService;

class PafCases extends BaseDoctrineService
{
    use SlugService;

    public function getCases($status = null)
    {
        if (!$status) {
            $status = [PafCase::STATUS_ACCEPTED, PafCase::STATUS_WIP];
        }

        return $this->repository->findBy(['status' => $status]);
    }
}

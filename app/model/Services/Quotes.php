<?php

namespace App\Model\Services;

use App\Model\Entity\Quote;
use Thoronir42\Model\BaseRepository;

class Quotes extends BaseRepository
{
    protected $entity_class = Quote::class;

    public function findForOverview()
    {
        $picked = $this->findBy(['status' => 'selected']);
        $new = $this->findBy(['status' => 'new']);

        return [
            'picked'
        ];
    }
}

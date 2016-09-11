<?php

namespace App\Model\Services;


use App\Model\Entity\Fursuit;
use Kdyby\Doctrine\EntityManager;
use Thoronir42\Model\BaseRepository;

class Fursuits extends BaseRepository
{
    protected $entity_class = Fursuit::class;
}

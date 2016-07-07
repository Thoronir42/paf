<?php

namespace App\Model\Services;


use App\Model\Entity\Fursuit;
use Kdyby\Doctrine\EntityManager;

class Fursuits extends BaseService
{
	public function __construct(EntityManager $em)
	{
		parent::__construct($em, $em->getRepository(Fursuit::class));
	}
}

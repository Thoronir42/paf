<?php

namespace App\Model\Services;


use App\Model\Entity\Quote;
use Kdyby\Doctrine\EntityManager;

class Quotes extends BaseService
{
	public function __construct(EntityManager $em)
	{
		parent::__construct($em, $em->getRepository(Quote::class));
	}

	public function findForOverview()
	{
		$picked = $this->findBy(['status' => 'selected']);
		$new = $this->findBy(['status' => 'new']);

		return [
			'picked'
		];
	}
}

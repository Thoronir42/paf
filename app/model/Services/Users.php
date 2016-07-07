<?php

namespace App\Model\Services;

use App\Model\Entity\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\DateTime;

class Users extends BaseService
{
	public function __construct(EntityManager $em)
	{
		parent::__construct($em, $em->getRepository(User::class));
	}
}

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

	public function create($username, $password){
		$check = $this->findOneBy(['username' => $username]);
		if($check){
			return false;
		}

		$user = new User();
		$user->username = $username;
		$user->password = $password;
		$user->registered = $user->lastActivity = DateTime::from('now');

		$this->save($user);

		return true;
	}
}

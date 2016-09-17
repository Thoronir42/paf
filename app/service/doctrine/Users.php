<?php

namespace App\Services\Doctrine;

use App\Model\Entity\User;
use Nette\Utils\DateTime;
use SeStep\Model\BaseDoctrineService;
use SeStep\Model\TProtoRepositoryAccess;

class Users extends BaseDoctrineService
{
    use TProtoRepositoryAccess;

	public function create($username, $password){
		$check = $this->repository->findOneBy(['username' => $username]);
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

<?php

namespace App\Model\Services;

use App\Model\Entity\User;
use Nette\Utils\DateTime;
use Thoronir42\Model\BaseRepository;

class Users extends BaseRepository
{
	protected $entity_class = User::class;

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

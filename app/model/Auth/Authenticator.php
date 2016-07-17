<?php

namespace App\Model\Auth;

use App\Model\Entity\User;
use App\Model\Entity\UserRole;
use App\Model\Services\UserRoles;
use App\Model\Services\Users;
use Nette;
use Nette\Object;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;
use Nette\Utils\DateTime;


/**
 * Users management.
 */
class Authenticator extends Object implements IAuthenticator
{
	/** @var Users */
	private $users;


	public function __construct(Users $users)
	{
		$this->users = $users;
	}


	/**
	 * Performs an authentication.
	 * @return Identity
	 * @throws AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($login, $password) = $credentials;

		/** @var User $user */
		$user = $this->users->findOneBy(['username' => $login]);

		if (!$user) {
			throw new AuthenticationException('Login was not recognised.', self::IDENTITY_NOT_FOUND);
		} elseif (!Passwords::verify($password, $user->password)) {
			throw new AuthenticationException('Entered password did not match the login.', self::INVALID_CREDENTIAL);
		}

		$arr = $user->toArray();
		unset($arr['password']);
		return new Identity($user->getId(), 'user', $arr);
	}
}

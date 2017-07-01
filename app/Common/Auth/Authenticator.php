<?php

namespace App\Common\Auth;

use App\Common\Model\Entity\User;
use App\Common\Services\Doctrine\Users;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;


/**
 * Users management.
 */
class Authenticator implements IAuthenticator
{
	/** @var Users */
	private $users;
    /** @var string[] */
    private $powerUsers;


    public function __construct(Users $users, $powerUsers = [])
	{
		$this->users = $users;
        $this->powerUsers = $powerUsers;
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
		} elseif (!Passwords::verify($password, $user->getPassword())) {
			throw new AuthenticationException('Entered password did not match the login.', self::INVALID_CREDENTIAL);
		}

		$arr = $user->toArray();
		unset($arr['password']);

		$role = in_array($user->getUsername(), $this->powerUsers) ? 'power-user' : 'user';

		return new Identity($user->getId(), $role, $arr);
	}
}

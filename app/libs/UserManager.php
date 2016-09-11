<?php

namespace App\Libs;

use Nette;
use Nette\Security\Passwords;


/**
 * Users management.
 */
class UserManager extends Nette\Security\SimpleAuthenticator
{
	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\DI\Container $container)
	{
		dump($container);exit;
	}


	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		$user = $this->findUser($username);

		if (!$user) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);

		} elseif (!strcmp($password, $user[self::COLUMN_PASSWORD_HASH])) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);

		} elseif (Passwords::needsRehash($user[self::COLUMN_PASSWORD_HASH])) {
			$user->update(array(
				self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
			));
		}

		$arr = $user->toArray();
		unset($arr[self::COLUMN_PASSWORD_HASH]);
		return new Nette\Security\Identity($user[self::COLUMN_ID], $user[self::COLUMN_ROLE], $arr);
	}

	private function findUser($username)
	{

	}

}



class DuplicateNameException extends \Exception
{}

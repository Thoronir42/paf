<?php declare(strict_types=1);

namespace PAF\Common\Security;


use Nette\Security\IIdentity;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;
use PAF\Modules\CommonModule\Model\User;
use PAF\Modules\CommonModule\Repository\UserRepository;


class Authenticator implements IAuthenticator
{
	/** @var UserRepository */
	private $userRepository;
    /** @var Passwords */
    private $passwords;
    /** @var string[] */
    private $powerUsers;


    public function __construct(UserRepository $userRepository, Passwords $passwords, $powerUsers = [])
	{
		$this->userRepository = $userRepository;
        $this->passwords = $passwords;
        $this->powerUsers = $powerUsers;
    }


    /**
     * Performs an authentication.
     * @param array $credentials
     * @return Identity
     * @throws AuthenticationException
     */
	public function authenticate(array $credentials): IIdentity
	{
		list($login, $password) = $credentials;

		/** @var User $user */
		$user = $this->userRepository->findOneBy(['username' => $login]);

		if (!$user) {
			throw new AuthenticationException('Login was not recognised.', self::IDENTITY_NOT_FOUND);
		} elseif (!$this->passwords->verify($password, $user->password)) {
			throw new AuthenticationException('Entered password did not match the login.', self::INVALID_CREDENTIAL);
		}

		// todo: use live-data instead of snapshot via custom UserStorage class
		$arr = $user->getRowData();
		unset($arr['password']);

		$role = in_array($user->username, $this->powerUsers) ? 'power-user' : 'user';

		return new Identity($user->id, $role, $arr);
	}
}

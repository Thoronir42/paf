<?php declare(strict_types=1);

namespace PAF\Common\Security;

use Nette\Security\IIdentity;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;
use PAF\Modules\CommonModule\Model\User;
use PAF\Modules\CommonModule\Services\Users;

final class Authenticator implements IAuthenticator
{
    /** @var Users */
    private $users;
    /** @var Passwords */
    private $passwords;

    public function __construct(Users $users, Passwords $passwords)
    {
        $this->users = $users;
        $this->passwords = $passwords;
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
        $user = $this->users->findOneByLogin($login);

        if (!$user) {
            // run a hash test to delay response to prevent exposure of existing accounts
            $this->passwords->hash('Wait up, yo');
            throw new AuthenticationException('authentication.incorrect-login', self::IDENTITY_NOT_FOUND);
        } elseif (!$this->passwords->verify($password, $user->password)) {
            throw new AuthenticationException('authentication.incorrect-password', self::INVALID_CREDENTIAL);
        }

        $identity = new LiveUserIdentity($user->id);
        $identity->initialize($user, $this->users->getRoles($user));

        return $identity;
    }
}

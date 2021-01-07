<?php declare(strict_types=1);

namespace PAF\Common\Security;

use DateTime;
use Nette\Security\IIdentity;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;
use PAF\Modules\CommonModule\Model\User;
use PAF\Modules\CommonModule\Services\Users;
use SeStep\Moment\HasMomentProvider;
use SeStep\NetteApi\JwtService;

final class Authenticator implements IAuthenticator
{
    use HasMomentProvider;

    /** @var Users */
    private $users;
    /** @var Passwords */
    private $passwords;
    private JwtService $jwtService;

    public function __construct(Users $users, Passwords $passwords, JwtService $jwtService)
    {
        $this->users = $users;
        $this->passwords = $passwords;
        $this->jwtService = $jwtService;
    }


    /**
     * Performs an authentication.
     * @param array $credentials
     * @return LiveUserIdentity
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials): LiveUserIdentity
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

    public function authenticateToken(array $credentials, DateTime $expireAt = null): string
    {
        $identity = $this->authenticate($credentials);
        if (!$expireAt) {
            $expireAt = $this->getMomentProvider()->now()->modify('+1 day');
        }

        return $this->createAuthToken($identity->getEntity(), $expireAt, 'credentials');
    }

    public function createAuthToken(User $user, DateTime $expireAt = null, string $source = null): string
    {
        $authData = [
            'id' => $user->id,
            'roles' => $this->users->getRoles($user),
        ];
        if ($expireAt) {
            $authData['expireAt'] = $expireAt->format(DateTime::ISO8601);
        }
        if ($source) {
            $authData['source'] = $source;
        }

        return $this->jwtService->encode($authData);
    }
}

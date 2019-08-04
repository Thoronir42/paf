<?php declare(strict_types=1);

namespace PAF\Common\Security;

use Nette\Http\UserStorage;
use Nette\InvalidStateException;
use Nette\Security\IIdentity;
use Nette\Security\IUserStorage;
use PAF\Modules\CommonModule\Services\Users;

final class LiveUserStorage implements IUserStorage
{

    const AUTHENTICATED = 'authenticated';

    /** @var UserStorage */
    private $userStorage;
    /** @var Users */
    private $users;

    public function __construct(UserStorage $userStorage, Users $users)
    {
        $this->userStorage = $userStorage;
        $this->users = $users;
    }

    /** @inheritDoc */
    public function setAuthenticated(bool $state)
    {
        $this->userStorage->setAuthenticated($state);
        return $this;
    }

    /** @inheritDoc */
    public function isAuthenticated(): bool
    {
        return $this->userStorage->isAuthenticated();
    }

    /** @inheritDoc */
    public function setIdentity(?IIdentity $identity)
    {
        if ($identity && !$identity instanceof LiveUserIdentity) {
            $identity = new LiveUserIdentity($identity->getId());
        }
        bdump($identity);

        $this->userStorage->setIdentity($identity);

        return $this;
    }

    /** @inheritDoc */
    public function getIdentity(): ?IIdentity
    {
        /** @var LiveUserIdentity $identity */
        $identity = $this->userStorage->getIdentity();

        if (!$identity->isInitialized()) {
            $this->initializeIdentity($identity);
        }

        return $identity;
    }

    /** @inheritDoc */
    public function setExpiration(?string $expire, int $flags = 0)
    {
        $this->userStorage->setExpiration($expire, $flags);
        return $this;
    }

    /** @inheritDoc */
    public function getLogoutReason(): ?int
    {
        return $this->userStorage->getLogoutReason();
    }

    private function initializeIdentity(LiveUserIdentity $identity): bool
    {
        $user = $this->users->findUserById($identity->getId());
        if (!$user) {
            $this->userStorage->setAuthenticated(false);
            // todo: log instead of throwing
            throw new InvalidStateException("Logged in user could not be found");
        }

        $roles = $this->users->getRoles($user);

        $identity->initialize($user, $roles);

        return true;
    }
}

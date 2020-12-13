<?php declare(strict_types=1);

namespace PAF\Modules\DirectoryModule\Services;

use Nette\Security;
use PAF\Common\Security\LiveUserIdentity;
use PAF\Modules\CommonModule\Model\User;
use PAF\Modules\DirectoryModule\Model\Person;

trait HasAppUser
{
    protected ?User $appUser = null;
    protected ?Person $dirPerson = null;

    public function injectAppUser(PersonService $personService, Security\User $user)
    {
        if (!$user->isLoggedIn()) {
            return;
        }

        /** @var LiveUserIdentity $identity */
        $identity = $user->identity;
        $this->appUser = $identity->getEntity();
        $this->dirPerson = $personService->findOneBy(['user' => $this->appUser]);
    }
}

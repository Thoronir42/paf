<?php declare(strict_types=1);

namespace PAF\Common\Presenter;

use PAF\Common\Security\LiveUserIdentity;
use PAF\Modules\CommonModule\Model\User;
use PAF\Modules\CommonModule\Services\Users;
use PAF\Modules\DirectoryModule\Model\Person;
use PAF\Modules\DirectoryModule\Services\PersonService;

trait HasAppUser
{
    /** @var User */
    protected $appUser;
    /** @var Person */
    protected $dirPerson;

    public function injectAppUser(PersonService $personService)
    {
        if (!$this->user->isLoggedIn()) {
            return;
        }

        /** @var LiveUserIdentity $identity */
        $identity = $this->user->identity;
        $this->appUser = $identity->getEntity();
        $this->dirPerson = $personService->findOneBy(['user' => $this->appUser]);
    }
}

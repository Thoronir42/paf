<?php declare(strict_types=1);

namespace PAF\Common\Security;

use Nette\Security\IIdentity;
use PAF\Modules\CommonModule\Model\User;

class LiveUserIdentity implements IIdentity
{

    /** @var mixed */
    private $id;

    /** @var string[] */
    private $roles;
    /** @var User */
    private $user;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the ID of user.
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a list of roles that the user is a member of.
     */
    public function getRoles(): array
    {
        return $this->isInitialized() ? $this->roles : [];
    }

    public function isInitialized(): bool
    {
        return !is_null($this->user);
    }

    public function initialize(User $user, array $roles = [])
    {
        $user->detach();
        $user->unsetProperty('password');

        $this->user = $user;
        $this->roles = $roles;
    }

    public function getData(string $key = null)
    {
        if (!$key) {
            return $this->user->getRowData();
        }

        return $this->user->$key;
    }

    public function __sleep()
    {
        return ['id'];
    }
}

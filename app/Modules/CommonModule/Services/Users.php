<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Services;

use PAF\Modules\CommonModule\Model\User;
use PAF\Modules\CommonModule\Repository\UserRepository;

class Users
{
    /** @var UserRepository */
    private $repository;
    /** @var string[] */
    private $powerUsers;

    public function __construct(UserRepository $repository, array $powerUsers = [])
    {
        $this->repository = $repository;
        $this->powerUsers = $powerUsers;
    }

    public function findUserById($id): ?User
    {
        return $this->repository->findOneBy([
            'id' => $id,
        ]);
    }

    public function findOneByLogin($login): ?User
    {
        return $this->repository->findOneBy([
            'username' => $login,
        ]);
    }

    /**
     * @param User|mixed $user - Username or User entity
     *
     * @return string[]
     */
    public function getRoles($user)
    {
        if ($user instanceof User) {
            $user = $user->username;
        }

        $roles = ['user'];
        if (in_array($user, $this->powerUsers)) {
            $roles[] = 'power-user';
        }

        return $roles;
    }
}

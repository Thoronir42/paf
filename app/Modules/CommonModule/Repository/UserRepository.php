<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Repository;


use PAF\Common\Model\BaseRepository;
use Nette\InvalidStateException;
use PAF\Modules\CommonModule\Model\User;

class UserRepository extends BaseRepository
{
    public function create(string $username, string $password): User
    {
        $check = $this->findOneBy(['username' => $username]);
        if ($check) {
            throw new InvalidStateException("User with username $username already exists");
        }

        $user = new User();
        $user->username = $username;
        $user->password = $password;

        return $user;
    }

    /**
     * @param $username
     * @return User|null
     */
    public function findOneByUsername($username)
    {
        return $this->repository->findOneBy(['username' => $username]);
    }
}

<?php

namespace App\Common\Services\Doctrine;

use App\Common\Model\Entity\User;
use Nette\InvalidStateException;
use SeStep\Model\BaseDoctrineService;
use SeStep\Model\TProtoRepositoryAccess;

class Users extends BaseDoctrineService
{
    use TProtoRepositoryAccess;


    /**
     * @param string $username
     * @param string $password
     *
     * @throws InvalidStateException
     *
     * @return User
     */
    public function create($username, $password)
    {
        $check = $this->repository->findOneBy(['username' => $username]);
        if ($check) {
            throw new InvalidStateException("User with username $username already exists");
        }

        return new User($username, $password);
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

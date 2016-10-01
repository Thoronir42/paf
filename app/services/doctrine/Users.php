<?php

namespace App\Services\Doctrine;

use App\Model\Entity\User;
use Nette\InvalidStateException;
use Nette\Utils\DateTime;
use SeStep\Model\BaseDoctrineService;
use SeStep\Model\TProtoRepositoryAccess;

class Users extends BaseDoctrineService
{
    use TProtoRepositoryAccess;


    public function create($username, $password)
    {
        $check = $this->repository->findOneBy(['username' => $username]);
        if ($check) {
            throw new InvalidStateException("User with username $username already exists");
        }

        return new User($username, $password);
    }

    public function findOneByUsername($username)
    {
        return $this->repository->findOneBy(['username' => $username]);
    }
}

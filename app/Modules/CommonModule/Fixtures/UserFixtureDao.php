<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Fixtures;

use LeanMapper\IMapper;
use Nette\Security\Passwords;
use SeStep\LeanFixtures\RepositoryFixtureDao;
use PAF\Modules\CommonModule\Model\User;
use PAF\Modules\CommonModule\Repository\UserRepository;
use SeStep\LeanFixtures\FixtureDao;

class UserFixtureDao implements FixtureDao
{
    private UserRepository $userRepository;
    private RepositoryFixtureDao $repoDao;
    private Passwords $passwords;

    public function __construct(UserRepository $userRepository, IMapper $mapper, Passwords $passwords)
    {
        $this->userRepository = $userRepository;
        $this->repoDao = new RepositoryFixtureDao($userRepository, $mapper);
        $this->passwords = $passwords;
    }

    public function getEntityClass(): string
    {
        return User::class;
    }

    public function create($entityData): int
    {
        if (isset($entityData['password'])) {
            $entityData['password'] = $this->passwords->hash($entityData['password']);
        }

        return $this->repoDao->create($entityData);
    }

    public function findBy($value): ?User
    {
        return $this->userRepository->findOneBy(['username' => $value]);
    }

    public function getPropertyRelatedClasses(): array
    {
        return $this->repoDao->getPropertyRelatedClasses();
    }
}

<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Fixtures;

use LeanMapper\IMapper;
use Nette\Security\Passwords;
use PAF\Common\Fixtures\RepositoryFixtureDao;
use PAF\Modules\CommonModule\Model\User;
use PAF\Modules\CommonModule\Repository\UserRepository;
use SeStep\LeanFixtures\FixtureDao;

class UserFixtureDao implements FixtureDao
{

    /** @var RepositoryFixtureDao */
    private $repoDao;
    /** @var Passwords */
    private $passwords;

    public function __construct(UserRepository $repository, IMapper $mapper, Passwords $passwords)
    {
        $this->repoDao = new RepositoryFixtureDao($repository, $mapper);
        $this->passwords = $passwords;
    }

    public function getEntityClass(): string
    {
        return User::class;
    }

    public function create($entityData)
    {
        if (isset($entityData['password'])) {
            $entityData['password'] = $this->passwords->hash($entityData['password']);
        }

        $this->repoDao->create($entityData);
    }

    public function getPropertyRelatedClasses(): array
    {
        return $this->repoDao->getPropertyRelatedClasses();
    }
}

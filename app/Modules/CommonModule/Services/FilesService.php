<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Services;

use PAF\Modules\CommonModule\Model\UserFile;
use PAF\Modules\CommonModule\Model\UserFileThread;
use PAF\Modules\CommonModule\Repository\UserFileRepository;
use PAF\Modules\CommonModule\Repository\UserFileThreadRepository;
use SeStep\Moment\HasMomentProvider;

class FilesService
{
    use HasMomentProvider;

    /** @var UserFileRepository */
    private $fileRepository;
    /** @var UserFileThreadRepository */
    private $threadRepository;

    public function __construct(UserFileRepository $fileRepository, UserFileThreadRepository $threadRepository)
    {
        $this->fileRepository = $fileRepository;
        $this->threadRepository = $threadRepository;
    }

    public function findFile(int $id): ?UserFile
    {
        return $this->fileRepository->find($id);
    }

    public function findThread(int $id): ?UserFileThread
    {
        return $this->threadRepository->find($id);
    }

    public function createThread($persist = false): UserFileThread
    {
        $thread = new UserFileThread();
        $thread->dateCreated = $this->getMomentProvider()->now();
        if ($persist) {
            $this->threadRepository->persist($thread);
        }

        return $thread;
    }

    /**
     * @param UserFile|UserFileThread $entity
     *
     * @return int
     */
    public function save($entity): int
    {
        if ($entity instanceof UserFile) {
            return $this->fileRepository->persist($entity);
        }
        return $this->threadRepository->persist($entity);
    }
}

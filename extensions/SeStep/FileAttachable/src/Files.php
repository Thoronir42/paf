<?php declare(strict_types=1);

namespace SeStep\FileAttachable;

use SeStep\FileAttachable\Model\UserFile;
use SeStep\FileAttachable\Model\UserFileThread;
use SeStep\FileAttachable\Service\UserFileRepository;
use SeStep\FileAttachable\Service\UserFileThreadRepository;

class Files
{
    /** @var UserFileRepository */
    private $fileRepository;
    /** @var UserFileThreadRepository */
    private $threadRepository;

    public function __construct(UserFileRepository $fileRepository, UserFileThreadRepository $threadRepository)
    {
        $this->fileRepository = $fileRepository;
        $this->threadRepository = $threadRepository;
    }

    public function findFile(int $id)
    {
        return $this->fileRepository->get($id);
    }

    public function findThread($id)
    {
        return $this->threadRepository->find($id);
    }

    public function createThread($persist = false)
    {
        $thread = new UserFileThread();
        $thread->dateCreated = new \DateTime();
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
    public function save($entity)
    {
        if ($entity instanceof UserFile) {
            return $this->fileRepository->persist($entity);
        }
        return $this->threadRepository->persist($entity);
    }
}

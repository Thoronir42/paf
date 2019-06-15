<?php declare(strict_types=1);

namespace SeStep\FileAttachable;


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

    public function findFile(int $id) {
        return $this->fileRepository->get($id);
    }

    public function findThread($id)
    {
        return $this->threadRepository->find($id);
    }

    public function createThread($persist = false, $flush = false)
    {
        $thread = new UserFileThread();
        if($persist) {
            $this->em->persist($thread);
            if($flush) {
                $this->em->flush();
            }
        }

        return $thread;
    }

    /**
     * @param FileEntity|UserFileThread $entity
     */
    public function save($entity, $flushImmediatelly = true) {
        $this->em->persist($entity);
        if($flushImmediatelly) {
            $this->em->flush($entity);
        }
    }


    public function flushAll() {
        $this->em->flush();
    }
}
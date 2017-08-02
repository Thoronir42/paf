<?php

namespace SeStep\FileAttachable\Service;


use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use SeStep\FileAttachable\Model\FileEntity;
use SeStep\FileAttachable\Model\FileThread;

class Files
{

    /** @var EntityManager */
    private $em;
    /** @var EntityRepository */
    private $files;
    /** @var EntityRepository */
    private $threads;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->files = $em->getRepository(FileEntity::class);
        $this->threads = $em->getRepository(FileThread::class);
    }

    public function findFile($id) {
        return $this->files->find($id);
    }

    public function findThread($id)
    {
        return $this->threads->find($id);
    }

    public function createThread($persist = false, $flush = false)
    {
        $thread = new FileThread();
        if($persist) {
            $this->em->persist($thread);
            if($flush) {
                $this->em->flush();
            }
        }

        return $thread;
    }

    /**
     * @param FileEntity|FileThread $entity
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

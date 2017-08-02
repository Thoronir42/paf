<?php

namespace SeStep\FileAttachable\Model;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use SeStep\Model\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="file_attachable__file_thread")
 *
 */
class FileThread extends BaseEntity
{
    use Identifier;

    /**
     * @var Collection|FileEntity[]
     * @ORM\OneToMany(targetEntity="FileEntity", mappedBy="thread", indexBy="id")
     */
    protected $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function addFile(FileEntity $fileEntity) {
        return $this->files->add($fileEntity);
    }

    public function removeFile(FileEntity $fileEntity) {
        return $this->files->removeElement($fileEntity);
    }

}

<?php

namespace SeStep\FileAttachable\Model;


use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Http\FileUpload;
use Nette\InvalidArgumentException;
use SeStep\Model\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="file_attachable__file")
 *
 */
class FileEntity extends BaseEntity
{
    use Identifier;

    /**
     * @var FileThread
     * @ORM\ManyToOne(targetEntity="FileThread", inversedBy="files")
     */
    protected $thread;

    /**
     * @var string
     * @ORM\Column(type="string", length=420)
     */
    protected $filename;

    /**
     * @var string
     * @ORM\Column(type="integer")
     */
    protected $size;


    public function __construct($filename, $size = 0)
    {
        if(strlen($filename) > 420) {
            throw new InvalidArgumentException("Filename must be at most 420 characters long");
        }
        if($size < 0) {
            throw new InvalidArgumentException("File size must be a positive number or zero");
        }

        $this->filename = $filename;
        $this->size = $size;
    }

    public function getFilename() {
        return $this->filename;
    }

    public function getSize() {
        return $this->size;
    }

}

<?php

namespace App\Common\Model\Entity;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use SeStep\FileAttachable\Model\FileThread;
use SeStep\Model\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="paf__quote")
 *
 */
class Quote extends BaseEntity
{
    const STATUS_NEW = 'new';
    const STATUS_SELECTED = 'selected';
    const STATUS_DENIED = 'denied';

    use Identifier;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(type="string", columnDefinition="ENUM('new', 'selected', 'wip', 'denied')")
     */
    protected $status;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @var string
     * @ORM\Column(type="integer")
     */
    protected $sleeveLength;

    /**
     * @var FileThread
     * @ORM\OneToOne(targetEntity="SeStep\FileAttachable\Model\FileThread")
     * @ORM\JoinColumn(name="photos_file_thread_id", referencedColumnName="id")
     */
    protected $photos;



    /**
     * @var string
     * @ORM\Column(type="string", length=2000)
     */
    protected $characterDescription;

    public function __construct()
    {

    }

    /** @return User */
    public function getUser()
    {
        return $this->user;
    }

    /** @param User $user */
    public function setUser(User $user = null)
    {
        $this->user = $user;
    }

    /** @return string */
    public function getStatus()
    {
        return $this->status;
    }

    /** @param string $status */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /** @return string */
    public function getName()
    {
        return $this->name;
    }

    /** @param string $name */
    public function setName($name)
    {
        $this->name = $name;
    }

    /** @return string */
    public function getType()
    {
        return $this->type;
    }

    /** @param string $type */
    public function setType($type)
    {
        $this->type = $type;
    }

    /** @return string */
    public function getSleeveLength()
    {
        return $this->sleeveLength;
    }

    /** @param string $sleeveLength */
    public function setSleeveLength($sleeveLength)
    {
        $this->sleeveLength = $sleeveLength;
    }

    /**
     * @return string
     */
    public function getCharacterDescription()
    {
        return $this->characterDescription;
    }

    /**
     * @param string $characterDescription
     */
    public function setCharacterDescription($characterDescription)
    {
        $this->characterDescription = $characterDescription;
    }

    /**
     * @return FileThread
     */
    public function getPhotos()
    {
        return $this->photos;
    }

}

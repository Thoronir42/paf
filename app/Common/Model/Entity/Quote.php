<?php

namespace App\Common\Model\Entity;

use App\Common\Model\Embeddable\Contact;
use App\Common\Model\Embeddable\FursuitSpecification;
use App\Common\Model\Exceptions\EnumValueException;
use App\Common\Model\Traits\Slug;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;
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
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_DENIED = 'denied';

    use Identifier;
    use Slug;

    /*
     * @var User
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     *
    protected $user;
     */

    /**
     * @var string
     * @ORM\Column(type="string", length=24)
     */
    protected $status;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $dateCreated;

    /**
     * @var Contact
     * @ORM\Embedded(class="App\Common\Model\Embeddable\Contact")
     */
    protected $contact;

    /**
     * @var FursuitSpecification
     * @ORM\Embedded(class="App\Common\Model\Embeddable\FursuitSpecification")
     */
    protected $fursuit;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $sleeveLength;

    /**
     * @var FileThread
     * @ORM\OneToOne(targetEntity="SeStep\FileAttachable\Model\FileThread")
     * @ORM\JoinColumn(name="photos_file_thread_id", referencedColumnName="id")
     */
    protected $referenes;


    protected $characterDescription;

    public function __construct(Contact $contact, FursuitSpecification $fursuitSpecification)
    {
        $this->dateCreated = new DateTime();
        $this->setStatus(self::STATUS_NEW);
        $this->setContact($contact);
        $this->setFursuit($fursuitSpecification);
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

    /** @return Contact */
    public function getContact()
    {
        return $this->contact;
    }

    /** @param Contact $contact */
    public function setContact(Contact $contact)
    {
        $this->contact = $contact;
    }

    /** @return FursuitSpecification */
    public function getFursuit()
    {
        return $this->fursuit;
    }

    /** @param FursuitSpecification $fursuit */
    public function setFursuit($fursuit)
    {
        $this->fursuit = $fursuit;
        $this->setSlug(Strings::webalize($fursuit->getName()));
    }

    /** @return FileThread */
    public function getReferenes()
    {
        return $this->referenes;
    }

    /** @param FileThread $thread */
    public function setReferences(FileThread $thread = null)
    {
        $this->referenes = $thread;
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_NEW,
            self::STATUS_ACCEPTED,
            self::STATUS_DENIED,
        ];
    }

}

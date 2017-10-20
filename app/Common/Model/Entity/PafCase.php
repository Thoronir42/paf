<?php

namespace App\Common\Model\Entity;


use App\Common\Model\Embeddable\Contact;
use App\Common\Model\Embeddable\FursuitProgress;
use App\Common\Model\Embeddable\FursuitSpecification;
use App\Common\Model\Traits\FursuitEntity;
use App\Common\Model\Traits\Slug;
use App\Common\Model\Traits\SoftDelete;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\DateTime;
use SeStep\Commentable\Model\CommentThread;
use SeStep\Model\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="paf__case")
 *
 */
class PafCase extends BaseEntity
{
    const STATUS_ACCEPTED = "accepted";
    const STATUS_WIP = "wip";
    const STATUS_FINISHED = "finished";
    const STATUS_CANCELLED = "cancelled";

    use Identifier;
    use FursuitEntity;

    /**
     * @var PafWrapper
     * @ORM\OneToOne(targetEntity="PafWrapper", mappedBy="case")
     */
    protected $wrapper;

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
     * @var string
     * @ORM\Column(type="string", length=24)
     */
    protected $status;

    /**
     * @var FursuitProgress
     * @ORM\Embedded(class="App\Common\Model\Embeddable\FursuitProgress")
     */
    protected $progress;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $dateAccepted;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $targetDate;

    /**
     * @var CommentThread
     * @ORM\OneToOne(targetEntity="SeStep\Commentable\Model\CommentThread")
     */
    protected $comments;

    public function __construct(Contact $contact, FursuitSpecification $fursuitSpecification)
    {
        $this->dateAccepted = new DateTime();
        $this->progress = new FursuitProgress();

        $this->setStatus(self::STATUS_ACCEPTED);
        $this->setContact($contact);
        $this->setFursuit($fursuitSpecification);
    }

    /**
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param Contact $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return FursuitSpecification
     */
    public function getFursuit()
    {
        return $this->fursuit;
    }

    /**
     * @param FursuitSpecification $fursuit
     */
    public function setFursuit($fursuit)
    {
        $this->fursuit = $fursuit;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * @param mixed $progress
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;
    }

    /**
     * @return DateTime
     */
    public function getTargetDate()
    {
        return $this->targetDate;
    }

    /**
     * @param DateTime $targetDate
     */
    public function setTargetDate($targetDate)
    {
        $this->targetDate = $targetDate;
    }

    /**
     * @return CommentThread
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param CommentThread $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }


    public static function getStatuses()
    {
        return [
            self::STATUS_ACCEPTED,
            self::STATUS_WIP,
            self::STATUS_FINISHED,
            self::STATUS_CANCELLED,
        ];
    }
}

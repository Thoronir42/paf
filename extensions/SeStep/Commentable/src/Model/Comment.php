<?php

namespace SeStep\Commentable\Model;


use App\Common\Model\Entity\User;
use DateTime;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use SeStep\Model\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="commentable__comment")
 *
 */
class Comment extends BaseEntity
{
    use Identifier;

    /**
     * @var CommentThread
     * @ORM\ManyToOne(targetEntity="CommentThread", inversedBy="comments")
     */
    protected $thread;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $createdOn;

    /**
     * @var String
     * @ORM\Column(type="string", length=1024)
     */
    protected $text;

    public function __construct(CommentThread $thread, $text = "")
    {
        $this->setThread($thread);
        $this->createdOn = new DateTime();
        $this->setText($text);
    }


    public function getThread(): CommentThread
    {
        return $this->thread;
    }

    public function setThread(CommentThread $thread)
    {
        $this->thread = $thread;
    }

    public function getCreatedOn(): DateTime
    {
        return $this->createdOn;
    }

    public function setCreatedOn(DateTime $createdOn)
    {
        $this->createdOn = $createdOn;
    }

    public function getText(): String
    {
        return $this->text;
    }

    public function setText(String $text)
    {
        $this->text = $text;
    }



}

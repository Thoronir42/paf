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
     * @var User
     * @ORM\ManyToOne(targetEntity="CommentThread", inversedBy="comments")
     */
    protected $thread;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    protected $createdOn;

    /**
     * @var DateTime
     * @ORM\Column(type="string")
     */
    protected $text;
}

<?php declare(strict_types=1);

namespace SeStep\Commentable\Model;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use SeStep\Model\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="commentable__comment_thread")
 *
 */
class CommentThread extends BaseEntity
{
    use Identifier;

    /**
     * @var Collection|Comment[]
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="thread")
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function addComment($comment)
    {
        $this->comments->add($comment);
    }
}

<?php declare(strict_types=1);

namespace SeStep\Commentable\Query;


use Kdyby\Persistence\Queryable;
use SeStep\Commentable\Model\Comment;
use SeStep\Commentable\Model\CommentThread;

class FindCommentsQuery
{
    private $qb;

    public function __construct(Queryable $queryable)
    {
        $this->qb = $queryable->createQueryBuilder()
            ->select('c')
            ->from(Comment::class, 'c');
    }

    public function orderByDateCreated($order = 'ASC') {
        $this->qb->orderBy('c.createdOn', $order);

        return $this;
    }

    /**
     * @param CommentThread|int $thread
     *
     * @return $this
     */
    public function byThread($thread) {
        if($thread instanceof CommentThread) {
            $this->qb->andWhere('c.thread = :thread');
        } else {
            $this->qb->andWhere('c.thread.id = :thread');
        }
        $this->qb->setParameter('thread', $thread);

        return $this;
    }

    public function execute() {
        return $this->qb->getQuery()->getResult();
    }
}

<?php declare(strict_types=1);

namespace SeStep\Commentable\Lean\Query;

use PAF\Common\Lean\BaseQueryObject;
use PAF\Common\Lean\IQueryable;
use SeStep\Commentable\Lean\Model\Comment;
use SeStep\Commentable\Lean\Model\CommentThread;

/**
 * Class FindCommentsQuery
 * @package SeStep\Commentable\Query
 *
 * @method Comment fetch()
 * @method Comment[] fetchAll()
 */
class FindCommentsQuery extends BaseQueryObject
{
    public function __construct(IQueryable $queryable)
    {
        parent::__construct($queryable, 'c');
    }

    public function orderByDateCreated($order = 'ASC')
    {
        $this->query->orderBy('c.created_on', $order);

        return $this;
    }

    /**
     * @param CommentThread|int $thread
     *
     * @return $this
     */
    public function byThread($thread)
    {
        $threadId = $thread instanceof CommentThread ? $thread->id : $thread;
        $this->query->where('c.thread_id = ?', $threadId);

        return $this;
    }
}

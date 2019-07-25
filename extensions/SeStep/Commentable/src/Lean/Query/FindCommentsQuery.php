<?php declare(strict_types=1);

namespace SeStep\Commentable\Query;


use Dibi\DataSource;
use PAF\Common\Model\BaseQueryObject;
use PAF\Common\Model\IQueryable;
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

    public function orderByDateCreated($order = 'ASC') {
        $this->dataSource->orderBy('c.createdOn', $order);

        return $this;
    }

    /**
     * @param CommentThread|int $thread
     *
     * @return $this
     */
    public function byThread($thread) {
        $threadId = $thread instanceof CommentThread ? $thread->id : $thread;
        $this->dataSource->where('c.thread = ?', $threadId);

        return $this;
    }
}

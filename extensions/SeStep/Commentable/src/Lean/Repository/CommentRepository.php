<?php declare(strict_types=1);

namespace SeStep\Commentable\Lean\Repository;

use Dibi\Fluent;
use PAF\Common\Model\BaseRepository;
use SeStep\Commentable\Lean\Model\CommentThread;

class CommentRepository extends BaseRepository
{
    public function getCommentFeedQuery(CommentThread $thread): Fluent
    {
        return $this->select('comment.id, comment.created_on AS instant', 'comment')
            ->where('comment.thread_id = ?', $thread->id);
    }
}

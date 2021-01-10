<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Repository;

use Dibi\Fluent;
use SeStep\LeanCommon\BaseRepository;
use PAF\Modules\CommonModule\Model\CommentThread;

class CommentRepository extends BaseRepository
{
    public function getCommentFeedQuery(CommentThread $thread): Fluent
    {
        return $this->select('comment.id, comment.created_on AS instant', 'comment')
            ->where('comment.thread_id = ?', $thread->id);
    }
}

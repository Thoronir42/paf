<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Services;

use PAF\Modules\Feed\Source\FeedSource;
use PAF\Common\Lean\LeanRepositoryFeedSource;
use PAF\Modules\CommonModule\Model\Comment;
use PAF\Modules\CommonModule\Model\CommentThread;
use PAF\Modules\CommonModule\Repository\CommentRepository;
use PAF\Modules\CommonModule\Repository\CommentThreadRepository;

class CommentsService
{
    /** @var CommentRepository */
    private $commentRepository;
    /** @var CommentThreadRepository */
    private $commentThreadRepository;

    public function __construct(CommentRepository $commentRepository, CommentThreadRepository $commentThreadRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->commentThreadRepository = $commentThreadRepository;
    }

    public function createNewThread(): CommentThread
    {
        $thread = new CommentThread();
        $this->commentThreadRepository->persist($thread);

        return $thread;
    }

    public function save(Comment $comment)
    {
        return $this->commentRepository->persist($comment);
    }

    public function delete(Comment $comment)
    {
        return $this->commentRepository->delete($comment);
    }

    public function getFeedSource(CommentThread $comments): FeedSource
    {
        $select = $this->commentRepository->getCommentFeedQuery($comments);
        return new LeanRepositoryFeedSource($this->commentRepository, $select);
    }
}
